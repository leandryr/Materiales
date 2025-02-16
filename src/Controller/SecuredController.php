<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Address;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Reporte;
use App\Entity\Documentados;
use App\Entity\Localidad;
use App\Entity\Planta;
use App\Entity\Transportista;
use App\Entity\Ruta;
use App\Entity\Area;
use App\Entity\Proveedor;
use App\Entity\Registro;

use App\Api\UserModel;

use App\Api\SemanaModel;
use App\Api\DocumentadoModel;
use App\Api\DocumentadoViewModel;

use App\Api\ReporteListaModel;
use App\Api\LocalidadModel;
use App\Api\ReporteFullModel;
use App\Api\ReporteFullModelView;

use App\Api\TransportistaModel;
use App\Api\PlantaModel;
use App\Api\RutaModel;
use App\Api\AreaModel;
use App\Api\ProveedorModel;
use App\Api\RegistroListModel;
use App\Api\RegistroFullModel;
use App\Api\TipoModel;

use App\Api\DescripcionModel;



use App\Form\UserRegType;
use App\Form\UserEditType;
use App\Form\ReportType;
use App\Form\DocumentadoType;
use App\Form\ReportEditType;
use App\Form\DocumentadoEditType;
use App\Form\RegistroType;
use App\Form\BusquedaType;
use App\Form\RegistroEditType;
use App\Form\BusquedaReporteType;
use App\Repository\RegistroRepository;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use \PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use \PhpOffice\PhpSpreadsheet\IOFactory as Factory;
use \PhpOffice\PhpSpreadsheet\Reader\IReadFilter as Filter;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

class SecuredController extends AbstractController
{
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->dateFormat = 'd-m-Y';
        $this->dateFormatRegistro = 'Y-m-d';

    }

    /**
     * @param mixed $data Usually an object you want to serialize
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function createApiResponse($data, $statusCode = 200)
    {
        return new JsonResponse($data, $statusCode, []);
    }

    public function getErrorsFromForm($form){
        return $form->getErrors();
    }

    public function returnValidation($validation)
    {
        if ($validation['error']['code'] == 0) {
            return $this->createApiResponse([
            'validation' => $validation
        ]);
        } else {
            return $this->createApiResponse([
            'validation' => $validation
        ], 300);
        }
    }

    /**
     *
     *
     */
    public function checkToken($email, $token, $role)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy([
          'email' => $email,
          'activo' => true,
        ]);
        $status =false;
        $error = ['mensaje' => '',
            'code' => 0];
        $datos = [
            'status' => $status
        ];
        if ($user) {
            if ($user->getToken()== $token) {
                $lastLogin = $user->getLastLogin();
                $dateStart = \DateTime::createFromFormat("Y m d h:i:s", $lastLogin);
                //Add 5 minuts
                $dateStart->add(new \DateInterval("PT30M"));
                $date = date("Y m d h:i:s");
                $dateEnd = \DateTime::createFromFormat("Y m d h:i:s", $date);
                if ($dateEnd > $dateStart) {
                    // cerrar sesion
                    $user->setToken("");
                    $status = false;
                    $error['mensaje'] = "Token expirado";
                    $error['code'] = 1;
                } else {
                    // actualizar el datetime del $lastLogin$date = date("Y m d h:i:s");
                    $rol = $user->getRoles();
                    if (in_array($rol[0], $role)) {
                        $user->setLastLogin($date);
                        $status = true;
                        $error['mensaje'] = "Sin error";
                        $error['code'] = 0;
                    } else {
                        $status = false;
                        $error['mensaje'] = "Sin rol requerido";
                        $error['code'] = 2;
                    }
                }
            } else {
                // cerrar sesion
                $user->setToken("");
                $status = false;
                $error['mensaje'] = "Token expirado";
                $error['code'] = 1;
            }

            $this->manager->persist($user);
            $this->manager->flush();
        } else {
            $status = false;
            $error['mensaje'] = "No se encontro al usuario";
            $error['code'] = 3;
        }

        $datos['status'] = $status;
        $datos['error'] = $error;
        return $datos;
    }

    /**
     * @Route("/api/sendEmail/{email}/{token}", name="sendEmail", methods={ "POST"})
     *
     */
    public function sendEmail($email, $token, Request $request, MailerInterface $mailer)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR']);
        if ($validation['status']) {
            $file = $request->files->get('file');

            $correo = $request->get('correo');
            $cccoreo = $request->get('cccorreo');
            $asunto = $request->get('asunto');
            $mensaje = $request->get('mensaje');


            $correo_enviar = (new Email())
            //->from(new Address($email, 'Fabien'))
              ->from(new Address($email))
              ->to($correo);
            if ($cccoreo) {
                $correo_enviar->cc($cccoreo);
            }

            $correo_enviar
                ->replyTo($email)
                ->priority(Email::PRIORITY_HIGH)
                ->subject($asunto)
                ->text($mensaje)
                ->html($mensaje);

            try {
                if ($file) {
                    $fileName = $file->getClientOriginalName();
                    $fileType = $file->getClientMimeType();
                    $validation['fileName'] = $fileName;
                    $file->move(
                        'build/',
                        $fileName
                    );
                    $correo_enviar->attachFromPath('build/'.$fileName, $fileName, $fileType);
                }
            } catch (\Exception $e) {
                $validation['err'] = $e;
            }
            $mailer->send($correo_enviar);

            $validation['correoEnviado'] = true;
        } else {
            $validation['correoEnviado'] = false;
        }

        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/deleteFile/{email}/{token}/{enlace}", name="deleteFile", methods={ "GET"})
     *
     */
    public function deleteFile($email, $token, $enlace)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        if ($validation['status']) {
            unlink('build/'.$enlace);
        }

        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/newUser/{email}/{token}", name="newUser", methods={"POST", "GET"})
     *
     */
    public function newUser($email, $token, Request $request)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR']);

        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);
            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }

            $form = $this->createForm(UserRegType::class, null, [
          'csrf_protection' => false,
        ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
              'errors' => $errors
          ], 400);
            }

            $name =   $form->get('name')->getData();
            $email =   $form->get('email')->getData();
            $password =   $form->get('password')->getData();
            $type = $form->get('type')->getData();
            $status =false;
            $validation = [
            'status' => $status
        ];

            if (is_null($name)||is_null($email)||is_null($password)||is_null($type)) {
                $status = false;
                $errorCode = 4;
                $errorMsg = "Completa los campos";
            } else {
                $user = new User();
                $user->setActivo(true);
                $user->setEmail($email);
                $user->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $password
                ));
                $user->setPlainPassword($password);
                $user->setNombre($name);


                $rol = '';
                if ($type == 'Administrador') {
                    $rol = 'ROLE_ADMINISTRADOR';
                } elseif ($type == 'Capturista') {
                    $rol = 'ROLE_CAPTURISTA';
                } elseif ($type == 'Personal') {
                    $rol = 'ROLE_PERSONAL';
                }
                $user->setRoles([$rol]);
                $this->manager->persist($user);
                $this->manager->flush();
            }

            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy([
              'email' => $email,
            ]);
            if ($user) {
                $status = true;
                $validation['item'] = $this->getUserModel($user);
                $errorCode = 0;
                $errorMsg = "Sin Error";
            } else {
                $status = false;
                $errorCode = 5;
                $errorMsg = "No se pudo guardar al usuario";
            }
            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }

    public function getUserModel($user)
    {
        $model = new UserModel();
        $model->id = $user->getId();
        $model->usuario = $user->getNombre();
        $model->correo = $user->getEmail();
        $model->contracena = $user->getPlainPassword();

        $rols = $user->getRoles();
        $rol = $rols[0];
        $tipo = '';
        if ($rol == 'ROLE_ADMINISTRADOR') {
            $tipo = "Administrador";
        } elseif ($rol == 'ROLE_CAPTURISTA') {
            $tipo = "Capturista";
        } elseif ($rol == 'ROLE_PERSONAL') {
            $tipo= "Personal";
        }

        $model->type = $tipo;
        $model->activo = $user->getActivo();
        ;

        return $model;
    }

    /**
     * @Route("/api/getUsers/{email}/{token}", name="getUsers", methods={"GET"})
     *
     */
    public function getUsers($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $users = $repository->findAll();
            foreach ($users as $user) {
                $models[] = $this->getUserModel($user);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/editUser/{email}/{token}", name="editUser", methods={"POST"})
     *
     */
    public function editUser($email, $token, Request $request)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR']);

        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);
            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }

            $form = $this->createForm(UserEditType::class, null, [
          'csrf_protection' => false,
        ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
              'errors' => $errors
          ], 400);
            }
            $id = $form->get('id')->getData();
            $name =   $form->get('usuario')->getData();
            $correo =   $form->get('correo')->getData();
            $password =   $form->get('contracena')->getData();
            $type =   $form->get('type')->getData();

            $status =false;
            $validation = [
            'status' => $status
        ];
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy([
              'id' => $id,
            ]);
            if ($user) {
                if (! ($user->getNombre() == $name)) {
                    $user->setNombre($name);
                }
                if (! ($user->getEmail() == $correo)) {
                    $user->setEmail($correo);
                }
                if (! ($user->getPlainPassword() == $password)) {
                    $user->setPassword($this->passwordEncoder->encodePassword(
                        $user,
                        $password
                    ));
                    $user->setPlainPassword($password);
                }

                $rol = '';
                if ($type == 'Administrador') {
                    $rol = 'ROLE_ADMINISTRADOR';
                } elseif ($type == 'Capturista') {
                    $rol = 'ROLE_CAPTURISTA';
                } elseif ($type == 'Personal') {
                    $rol = 'ROLE_PERSONAL';
                }
                $user->setRoles([$rol]);

                $this->manager->persist($user);
                $this->manager->flush();
                $status = true;
                $validation['item'] = $this->getUserModel($user);
                $errorCode = 0;
                $errorMsg = "Sin Error";
            } else {
                $status = false;
                $errorCode = 5;
                $errorMsg = "No se pudo encontrar un usuario con ese id";
            }

            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/deleteUser/{email}/{token}/{id}", name="deleteUser", methods={"DELETE"})
     *
     */
    public function deleteUser($email, $token, $id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR']);
        $status =false;
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy([
              'id' => $id,
            ]);
            if ($user) {
                $this->manager->remove($user);
                $this->manager->flush();
                $status = true;
                $errorCode = 0;
                $errorMsg = "Sin Error";
            } else {
                $status = false;
                $errorCode = 4;
                $errorMsg = "No se encontro al usuario";
            }
        }
        $validation['status'] = $status;
        $validation['error']['code'] = $errorCode;
        $validation['error']['mensaje'] = $errorMsg;

        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/deactivateUser/{email}/{token}/{id}", name="deactivateUser", methods={"GET"})
     *
     */
    public function deactivateUser($email, $token, $id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR']);
        $status =false;
        $errorCode = 0;
        $errorMsg = "Sin Error";
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy([
              'id' => $id,
            ]);
            if ($user) {
                if ($user->getActivo()) {
                    $user->setActivo(false);
                } else {
                    $user->setActivo(true);
                }
                $this->manager->persist($user);
                $this->manager->flush();
                $status = true;
                $errorCode = 0;
                $errorMsg = "Sin Error";
            } else {
                $status = false;
                $errorCode = 4;
                $errorMsg = "No se encontro al usuario";
            }
        }
        $validation['status'] = $status;
        $validation['error']['code'] = $errorCode;
        $validation['error']['mensaje'] = $errorMsg;

        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/newReport/{email}/{token}", name="newReport", methods={"POST", "GET"})
     *
     */
    public function newReport($email, $token, Request $request)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA','ROLE_PERSONAL']);

        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);
            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }


            $form = $this->createForm(DocumentadoType::class, null, [
              'csrf_protection' => false,
            ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
                    'errors' => $errors
                ], 400);
            }
            $status =false;
            $validation = [
              'status' => $status
            ];

            $localidad =   $form->get('localidad')->getData();
            $claim =   $form->get('claim')->getData();
            $codigo =   $form->get('codigo')->getData();
            $planta =   $form->get('planta')->getData();
            $numero =   $form->get('numero')->getData();
            $cantidad =   $form->get('cantidad')->getData();
            $fechaNotificacion =   $form->get('fechaNotificacion')->getData();
            $perdidaSinFlete =   $form->get('perdidaSinFlete')->getData();
            $perdidaConFlete =   $form->get('perdidaConFlete')->getData();
            $area =   $form->get('area')->getData();
            $estatus =   $form->get('estatus')->getData();
            $documentacionFaltante =   $form->get('documentacionFaltante')->getData();

            $reporte = new Documentados();

            if ($localidad) {
                $localidadRepository = $this->getDoctrine()->getRepository(Localidad::class);
                $local = $localidadRepository->findOneBy([
                  'localidad' => $localidad
                ]);
                if ($local) {
                    $reporte->setLocalidad($local);
                } else {
                    $reporte->setLocalidad(null);
                }
            } else {
                $reporte->setLocalidad(null);
            }
            if ($planta) {
                $plantaRepository = $this->getDoctrine()->getRepository(Planta::class);
                $plant = $plantaRepository->findOneBy([
                  'planta' => $planta
                ]);
                if ($plant) {
                    $reporte->setPlanta($plant);
                } else {
                    $reporte->setPlanta(null);
                }
            } else {
                $reporte->setPlanta(null);
            }
            $reporte->setClaim($claim);

            $reporte->setCodigo($codigo);
            $reporte->setNumero($numero);
            $reporte->setCantidad($cantidad);
            $reporte->setPerdidaSinFlete($perdidaSinFlete);
            $reporte->setPerdidaConFlete($perdidaConFlete);


            if (is_null($fechaNotificacion) || $fechaNotificacion == '') {
                $reporte->setFechaNotificacion(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaNotificacion);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaNotificacion($fecha);
            }

            $reporte->setArea($area);
            $reporte->setEstatus($estatus);
            $reporte->setDocumentacionFaltante($documentacionFaltante);


            $this->manager->persist($reporte);
            $this->manager->flush();

            $status = true;
            $errorCode = 0;
            $errorMsg = "Sin Error";

            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/editReport/{email}/{token}", name="editReport", methods={"POST", "GET"})
     *
     */
    public function editReport($email, $token, Request $request)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA']);

        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);
            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }


            $form = $this->createForm(ReportEditType::class, null, [
              'csrf_protection' => false,
            ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
                    'errors' => $errors
                ], 400);
            }
            $status =false;
            $validation = [
              'status' => $status
            ];
            $id =   $form->get('id')->getData();

            $localidad =   $form->get('localidad')->getData();
            $claim =   $form->get('claim')->getData();
            $transportista =   $form->get('transportista')->getData();
            $tipo =   $form->get('tipo')->getData();
            $reclamadoUSD =   $form->get('reclamadoUSD')->getData();
            $reclamadoMXN =   $form->get('reclamadoMXN')->getData();
            $excedenteMXN =   $form->get('excedenteMXN')->getData();
            $estimadoMXN =   $form->get('estimadoMXN')->getData();
            $aceptadoMXN =   $form->get('aceptadoMXN')->getData();
            $rechazadoMXN = $form->get('rechazadoMXN')->getData();
            $canceladoMXN =   $form->get('canceladoMXN')->getData();
            $flete =   $form->get('flete')->getData();
            $fechaEvento =   $form->get('fechaEvento')->getData();
            $fechaEmision =   $form->get('fechaEmision')->getData();
            $anoEvento =   $form->get('anoEvento')->getData();
            $anoAsignacion =   $form->get('anoAsignacion')->getData();
            $anoDocumentacion =   $form->get('AnoDocumentacion')->getData();
            $formaPago =   $form->get('formaPago')->getData();

            $fechaRespuesta =   $form->get('fechaRespuesta')->getData();
            $fechaSolicitud =   $form->get('fechaSolicitud')->getData();
            $fechaAplicacion =   $form->get('fechaAplicacion')->getData();
            $fechaEscalacion =   $form->get('fechaEscalacion')->getData();
            $fechaResolucion =   $form->get('fechaResolucion')->getData();
            $area =   $form->get('area')->getData();
            $estatus =   $form->get('estatus')->getData();
            $observaciones =   $form->get('observaciones')->getData();

            $reporteRepository = $this->getDoctrine()->getRepository(Reporte::class);

            $reporte = $reporteRepository->find($id);

            $createdDate = new \DateTime();
            $reporte->setActualizacion($createdDate);

            if ($localidad) {
                $localidadRepository = $this->getDoctrine()->getRepository(Localidad::class);
                $local = $localidadRepository->findOneBy([
                  'localidad' => $localidad
                ]);
                if ($local) {
                    $reporte->setLocalidad($local);
                } else {
                    $reporte->setLocalidad(null);
                }
            } else {
                $reporte->setLocalidad(null);
            }

            $reporte->setClaim($claim);

            if ($transportista) {
                $transportistaRepository = $this->getDoctrine()->getRepository(Transportista::class);
                $trans = $transportistaRepository->findOneBy([
                'transportista' => $transportista
              ]);
                if ($trans) {
                    $reporte->setTransportista($trans);
                } else {
                    $reporte->setTransportista(null);
                }
            } else {
                $reporte->setTransportista(null);
            }

            $reporte->setTipo($tipo);
            $reporte->setReclamadoUSD($reclamadoUSD);
            $reporte->setReclamadoMXN($reclamadoMXN);
            $reporte->setExcedenteMXN($excedenteMXN);
            $reporte->setEstimadoMXN($estimadoMXN);
            $reporte->setAceptadoMXN($aceptadoMXN);
            $reporte->setRechazadoMXN($rechazadoMXN);
            $reporte->setCanceladoMXN($canceladoMXN);
            $reporte->setFlete($flete);



            if (is_null($fechaEvento) || $fechaEvento == '') {
                $reporte->setFechaEvento(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaEvento);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaEvento($fecha);
            }

            if (is_null($fechaEmision)|| $fechaEmision == '') {
                $reporte->setFechaEmision(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaEmision);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaEmision($fecha);
            }


            if (is_null($fechaEscalacion)|| $fechaEscalacion == '') {
                $reporte->setFechaEscalacion(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaEscalacion);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaEscalacion($fecha);
            }

            if (is_null($fechaResolucion)|| $fechaResolucion == '') {
                $reporte->setFechaResolucion(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaResolucion);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaResolucion($fecha);
            }

            if (is_null($fechaRespuesta)|| $fechaRespuesta == '') {
                $reporte->setFechaRespuesta(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaRespuesta);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaRespuesta($fecha);
            }

            if (is_null($fechaSolicitud)|| $fechaSolicitud == '') {
                $reporte->setFechaSolicitud(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaSolicitud);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaSolicitud($fecha);
            }

            if (is_null($fechaAplicacion)|| $fechaAplicacion == '') {
                $reporte->setFechaAplicacion(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaAplicacion);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaAplicacion($fecha);
            }

            $reporte->setAnoEvento($anoEvento);
            $reporte->setAnoAsignacion($anoAsignacion);
            $reporte->setAnoDocumentacion($anoDocumentacion);

            $reporte->setArea($area);
            $reporte->setEstatus($estatus);
            $reporte->setObservaciones($observaciones);


            $this->manager->persist($reporte);
            $this->manager->flush();

            $status = true;
            $errorCode = 0;
            $errorMsg = "Sin Error";

            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }


    /**
     * @Route("/api/editDocumentado/{email}/{token}", name="editDocumentado", methods={"POST", "GET"})
     *
     */
    public function editDocumentado($email, $token, Request $request)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA']);

        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);
            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }


            $form = $this->createForm(DocumentadoEditType::class, null, [
              'csrf_protection' => false,
            ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
                    'errors' => $errors
                ], 400);
            }
            $status =false;
            $validation = [
              'status' => $status
            ];
            $id =   $form->get('id')->getData();
            $localidad =   $form->get('localidad')->getData();
            $claim =   $form->get('claim')->getData();
            $codigo =   $form->get('codigo')->getData();
            $planta =   $form->get('planta')->getData();
            $numero =   $form->get('numero')->getData();
            $cantidad =   $form->get('cantidad')->getData();
            $fechaNotificacion =   $form->get('fechaNotificacion')->getData();
            $perdidaSinFlete =   $form->get('perdidaSinFlete')->getData();
            $perdidaConFlete= $form->get('perdidaConFlete')->getData();
            $area =   $form->get('area')->getData();
            $estatus =   $form->get('estatus')->getData();
            $documentacionFaltante =   $form->get('documentacionFaltante')->getData();

            $reporteRepository = $this->getDoctrine()->getRepository(Documentados::class);

            $reporte = $reporteRepository->find($id);


            if ($localidad) {
                $localidadRepository = $this->getDoctrine()->getRepository(Localidad::class);
                $local = $localidadRepository->findOneBy([
                  'localidad' => $localidad
                ]);
                if ($local) {
                    $reporte->setLocalidad($local);
                } else {
                    $reporte->setLocalidad(null);
                }
            } else {
                $reporte->setLocalidad(null);
            }
            if ($planta) {
                $plantaRepository = $this->getDoctrine()->getRepository(Planta::class);
                $plant = $plantaRepository->findOneBy([
                  'planta' => $planta
                ]);
                if ($plant) {
                    $reporte->setPlanta($plant);
                } else {
                    $reporte->setPlanta(null);
                }
            } else {
                $reporte->setPlanta(null);
            }
            $reporte->setClaim($claim);

            $reporte->setCodigo($codigo);
            $reporte->setNumero($numero);
            $reporte->setCantidad($cantidad);
            $reporte->setPerdidaSinFlete($perdidaSinFlete);
            $reporte->setPerdidaConFlete($perdidaConFlete);


            if (is_null($fechaNotificacion) || $fechaNotificacion == '') {
                $reporte->setFechaNotificacion(null);
            } else {
                try {
                    $fecha = new \DateTime($fechaNotificacion);
                } catch (\Exception $e) {
                    $fecha = null;
                }
                $reporte->setFechaNotificacion($fecha);
            }

            $reporte->setArea($area);
            $reporte->setEstatus($estatus);
            $reporte->setDocumentacionFaltante($documentacionFaltante);


            $this->manager->persist($reporte);
            $this->manager->flush();

            $status = true;
            $errorCode = 0;
            $errorMsg = "Sin Error";

            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }


    /**
     * @Route("/api/ingresarDocumentado/{email}/{token}/{id}", name="ingresarDocumentado", methods={ "GET"})
     *
     */
    public function ingresarDocumentado($email, $token, $id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA']);

        if ($validation['status']) {
            $status =false;
            $validation = [
              'status' => $status
            ];

            $documentadoRepository = $this->getDoctrine()->getRepository(Documentados::class);

            $documentado = $documentadoRepository->find($id);

            $localidad =   $documentado->getLocalidad();
            $claim =   $documentado->getClaim();
            $codigo =   $documentado->getCodigo(); //
            $planta =   $documentado->getPlanta();
            $numero =   $documentado->getNumero(); //
            $cantidad =   $documentado->getCantidad(); //
            $fechaNotificacion =  $documentado->getFechaNotificacion();
            $perdidaSinFlete =   $documentado->getPerdidaSinFlete(); //
            $perdidaConFlete= $documentado->getPerdidaConFlete();//
            $area =  $documentado->getArea();
            $estatus =   $documentado->getEstatus();
            $documentacionFaltante =   $documentado->getDocumentacionFaltante();


            $registro = new Registro();

            $createdDate = new \DateTime();
            $registro->setActualizacion($createdDate);
            $registro->setReferencia($claim);
            $registro->setLocalidad($localidad);
            $registro->setPlanta($planta);
            $registro->setEstatus($estatus);
            $observaciones = "CODIGO: ".$codigo." ; NÚMERO: ".$numero." ; CANTIDAD: ".$cantidad." ; AREA: ".$area." ; PERDIDA SIN FLETE: ".$perdidaSinFlete." ; PERDIDA CON FLETE: ". $perdidaConFlete." ; DOCUMENTACIÓN FALTANTE: ".$documentacionFaltante;
            $registro->setObservaciones($observaciones);

            $this->manager->persist($registro);
            $this->manager->remove($documentado);

            $this->manager->flush();

            $status = true;
            $errorCode = 0;
            $errorMsg = "Sin Error";
            $validation['item']  = $this->getRegistroFullModel($registro);
            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }


    /**
     * @Route("/api/getReportes/{email}/{token}", name="getReportes", methods={"POST"})
     *
     */
    public function getReportes(Request $request, $email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA', 'ROLE_PERSONAL']);
        $semanas = [];
        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }

            $form = $this->createForm(BusquedaReporteType::class, null, [
              'csrf_protection' => false,
              ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
                  'errors' => $errors
                ], 400);
            }
        //    $fechaEvento1 = $form->get('fechaEvento1')->getData();
        //    $fechaEvento2 = $form->get('fechaEvento2')->getData();
            $estatus = $form->get('estatus')->getData();
            $tipoReporte = $form->get('tipoReporte')->getData();
 /*
            if (is_null($fechaEvento1)) {
                $fechaEvento1 = 'no';
            } else {
                try {
                    $date4 =  new \DateTime($fechaEvento1);
                    $fechaEvento1 = $date4->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaEvento1 = 'no';
                }
            }
            if (is_null($fechaEvento2)) {
                $fechaEvento2 = 'no';
            } else {
                try {
                    $date5 =  new \DateTime($fechaEvento2);
                    $fechaEvento2 = $date5->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaEvento2 = 'no';
                }
            }
            */

            if (is_null($tipoReporte)) {
                $tipoReporte = 'no';
            }
            if (is_null($estatus)) {
                $estatus = 'no';
            }

            $repository = $this->getDoctrine()->getRepository(Registro::class);

            //$reportes = $repository->findAllOrdered($fechaEvento1, $fechaEvento2, $estatus);
          //  $reportes = $repository->findAllOrdered2( $estatus);

            //if ($reportes) {
              $hoy = new \DateTime();
              $hoyDia = $hoy->format('w');
              $viernes = $hoy;
              if (intval($hoyDia) >= 5) {
                $viernes->sub(new \DateInterval("P".strval(intval($hoyDia-5))."D"));
              }else {
                $viernes->sub(new \DateInterval("P".strval(intval($hoyDia+2))."D"));
              }

              $inicioSemana = new \DateTime($viernes->format('Y-m-d'));
              $finSemana = new \DateTime($inicioSemana->format('Y-m-d'));
              $finSemana->add(new \DateInterval("P6D"));

              $semanas[] = $this->getSemanaModel2($inicioSemana, $finSemana, $estatus, $tipoReporte);

            //}
        }
        $validation['items']= array_reverse($semanas);
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getDocumentados/{email}/{token}", name="getDocumentados", methods={"GET"})
     *
     */
    public function getDocumentados($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA', 'ROLE_PERSONAL']);
        $documentados = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Documentados::class);
            $docs = $repository->findDocumentadosOrdered();
            $llave = 0;
            if ($docs) {
                foreach ($docs as $doc) {
                    $documentados[] = $this->getDocumentadosModelView($doc);
                }
            }
        }

        $validation['items']= array_reverse($documentados);
        return $this->returnValidation($validation);
    }


    public function getMes($mes)
    {
        $mesTexto = '';
        switch ($mes) {
       case 1:
         $mesTexto = 'Enero';
         break;
       case 2:
         $mesTexto = 'Febrero';
         break;
       case 3:
         $mesTexto = 'Marzo';
         break;
       case 4:
         $mesTexto = 'Abril';
         break;
       case 5:
         $mesTexto = 'Mayo';
         break;
       case 12:
         $mesTexto = 'Diciembre';
         break;
       case 6:
         $mesTexto = 'Junio';
         break;
       case 7:
         $mesTexto = 'Julio';
         break;
       case 8:
         $mesTexto = 'Agosto';
         break;
       case 9:
         $mesTexto = 'Septiembre';
         break;
       case 10:
         $mesTexto = 'Octubre';
         break;
       case 11:
         $mesTexto = 'Noviembre';
         break;
       default:
         $mesTexto = '---';
         break;
     }
        return $mesTexto;
    }

    public function getSemanaModel($inicio, $fin, $fechaEvento1, $fechaEvento2, $estatus, $tipoReporte)
    {
        $model = new SemanaModel();

        $diaInicio = $inicio->format('j');
        $diaFin = $fin->format('j');
        $mesInicio = $inicio->format('n');
        $mesFin = $fin->format('n');
        $anoInicio = $inicio->format('Y');
        $anoFin = $fin->format('Y');

        $title = ''.$this->getMes($mesInicio).' '.$diaInicio.' - '.$this->getMes($mesFin).' '.$diaFin;
        $ano = '';
        if ($anoInicio == $anoFin) {
            $ano = $anoInicio;
        } else {
            $ano = ''.$anoInicio.' - '.$anoFin;
        }


        $model->semana = $inicio->format('Y-m-d');
        $model->title = $title;
        $model->ano = $ano;

        $repository = $this->getDoctrine()->getRepository(Registro::class);

        $reportesSemanal = $repository->findSemanales($inicio, $fin, $estatus);

        $reportesMenores = [];
        $reportesMayores = [];

        foreach ($reportesSemanal as $reporte) {
          $emisionHoy = 0;
          if ($reporte->getFechaEmision()) {
              $interval = date_diff($reporte->getFechaEmision(), new \DateTime());
              $emisionHoy = $interval->format('%a');
          }
          if ($tipoReporte == 'menores') {
              if ($emisionHoy < 45) {
                $reportesMenores[] = $this->getReporteListaModel($reporte);
              }

          } else {
              if ($tipoReporte == 'mayores') {
                if ($emisionHoy >= 45) {
                  $reportesMayores[] = $this->getReporteListaModel($reporte);
                }
              } elseif ($tipoReporte == 'no') {
                if ($emisionHoy < 45) {
                  $reportesMenores[] = $this->getReporteListaModel($reporte);
                }
                if ($emisionHoy >= 45) {
                  $reportesMayores[] = $this->getReporteListaModel($reporte);
                }
              }
          }
        }


        $model->reportesMayores = $reportesMayores;
        $model->reportesMenores = $reportesMenores;

        return $model;
    }

    public function getSemanaModel2($inicio, $fin, $estatus, $tipoReporte)
    {
        $model = new SemanaModel();

        $diaInicio = $inicio->format('j');
        $diaFin = $fin->format('j');
        $mesInicio = $inicio->format('n');
        $mesFin = $fin->format('n');
        $anoInicio = $inicio->format('Y');
        $anoFin = $fin->format('Y');

        $title = ''.$this->getMes($mesInicio).' '.$diaInicio.' - '.$this->getMes($mesFin).' '.$diaFin;
        $ano = '';
        if ($anoInicio == $anoFin) {
            $ano = $anoInicio;
        } else {
            $ano = ''.$anoInicio.' - '.$anoFin;
        }


        $model->semana = $inicio->format('Y-m-d');
        $model->title = $title;
        $model->ano = $ano;

        $repository = $this->getDoctrine()->getRepository(Registro::class);

        $reportesSemanal = $repository->findSemanales2($estatus);

        $reportesMenores = [];
        $reportesMayores = [];

        foreach ($reportesSemanal as $reporte) {
          $emisionHoy = 0;
          if ($reporte->getFechaEmision()) {
              $interval = date_diff($reporte->getFechaEmision(), new \DateTime());
              $emisionHoy = $interval->format('%a');
          }
          if ($tipoReporte == 'menores') {
              if ($emisionHoy < 45) {
                $reportesMenores[] = $this->getReporteListaModel($reporte);
              }

          } else {
              if ($tipoReporte == 'mayores') {
                if ($emisionHoy >= 45) {
                  $reportesMayores[] = $this->getReporteListaModel($reporte);
                }
              } elseif ($tipoReporte == 'no') {
                if ($emisionHoy < 45) {
                  $reportesMenores[] = $this->getReporteListaModel($reporte);
                }
                if ($emisionHoy >= 45) {
                  $reportesMayores[] = $this->getReporteListaModel($reporte);
                }
              }
          }
        }


        $model->reportesMayores = $reportesMayores;
        $model->reportesMenores = $reportesMenores;

        return $model;
    }


    public function getDocumentadosModel($doc)
    {
        $model = new DocumentadoModel();


        $model->claim = $doc->getClaim();
        if ($doc->getLocalidad()) {
            $model->localidad = $doc->getLocalidad()->getLocalidad();
        } else {
            $model->localidad = "";
        }

        $model->codigo = $doc->getCodigo();
        if ($doc->getPlanta()) {
            $model->planta = $doc->getPlanta()->getPlanta();
        } else {
            $model->planta = "";
        }
        $model->numero = $doc->getNumero();
        $model->cantidad = $doc->getCantidad();
        $model->fecha = $doc->getFechaNotificacion()->format('Y-m-d');
        $model->perdidaSinFlete = $doc->getPerdidaSinFlete();
        $model->perdidaConFlete = $doc->getPerdidaConFlete();
        $model->documentacionFaltante = $doc->getDocumentacionFaltante();
        $model->area = $doc->getArea();
        $model->estatus = $doc->getEstatus();


        return $model;
    }

    public function getDocumentadosModelView($doc)
    {
        $model = new DocumentadoViewModel();
        $model->id = $doc->getId();

        $model->claim = $doc->getClaim();
        if ($doc->getLocalidad()) {
            $model->localidad = $doc->getLocalidad()->getLocalidad();
        } else {
            $model->localidad = "";
        }

        $model->codigo = $doc->getCodigo();
        if ($doc->getPlanta()) {
            $model->planta = $doc->getPlanta()->getPlanta();
        } else {
            $model->planta = "";
        }
        $model->numero = $doc->getNumero();
        $model->cantidad = $doc->getCantidad();
        $model->fecha = $doc->getFechaNotificacion()->format('Y-m-d');
        $model->perdidaSinFlete = $doc->getPerdidaSinFlete();
        $model->perdidaConFlete = $doc->getPerdidaConFlete();
        $model->documentacionFaltante = $doc->getDocumentacionFaltante();
        $model->area = $doc->getArea();
        $model->estatus = $doc->getEstatus();


        return $model;
    }


    public function getSemanaDocumentadaModel($inicio, $fin)
    {
        $model = new SemanaDocumentadaModel();

        $diaInicio = $inicio->format('j');
        $diaFin = $fin->format('j');
        $mesInicio = $inicio->format('n');
        $mesFin = $fin->format('n');
        $anoInicio = $inicio->format('Y');
        $anoFin = $fin->format('Y');

        $title = ''.$this->getMes($mesInicio).' '.$diaInicio.' - '.$this->getMes($mesFin).' '.$diaFin;
        $ano = '';
        if ($anoInicio == $anoFin) {
            $ano = $anoInicio;
        } else {
            $ano = ''.$anoInicio.' - '.$anoFin;
        }


        $model->semana = $inicio->format('Y-m-d');
        $model->title = $title;
        $model->ano = $ano;

        $repository = $this->getDoctrine()->getRepository(Reporte::class);

        $reportesSemanal = $repository->findSemanalesDocumentados($inicio, $fin);

        $reportes = [];
        foreach ($reportesSemanal as $reporte) {
            $reportes[] = $this->getReporteListaModel($reporte);
        }

        $model->reportes = $reportes;
        return $model;
    }


    public function getReporteListaModel($reporte)
    {
        $model = new ReporteListaModel();
        $model->id = $reporte->getId();
        $model->claim = $reporte->getReferencia();
        if ($reporte->getLocalidad()) {
            $model->localidad = $reporte->getLocalidad()->getLocalidad();
        } else {
            $model->localidad = '';
        }
        if ($reporte->getTransportista()) {
            $model->transportista = $reporte->getTransportista()->getTransportista();
        } else {
            $model->transportista = '';
        }

        $model->tipo = $reporte->getTipo();
        if ($reporte->getFechaEmision()) {
            $model->fecha = $reporte->getFechaEmision()->format('Y-m-d');
        } else {
            $model->fecha = '';
        }
        $model->estatus = $reporte->getEstatus();

        return $model;
    }

    /**
     * @Route("/api/getExcelSemanal/{email}/{token}/{estatus}/{tipoReporte}", name="getExcelSemanal", methods={ "GET"})
     *
     */
    public function getExcelSemanal($email, $token, $estatus, $tipoReporte)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        if ($validation['status']) {

            $repository = $this->getDoctrine()->getRepository(Registro::class);

            $reportesSemanal = $repository->findSemanales2( $estatus);
            $reportesMayores = [];
            $reportesMenores = [];


            foreach ($reportesSemanal as $reporte) {
              $emisionHoy = 0;
              if ($reporte->getFechaEmision()) {
                  $interval = date_diff($reporte->getFechaEmision(), new \DateTime());
                  $emisionHoy = $interval->format('%a');
              }
              if ($tipoReporte == 'menores') {
                  if ($emisionHoy < 45) {
                    $reportesMenores[] = $this->getReporteMenorDescargaModel($reporte);
                  }

              } else {
                  if ($tipoReporte == 'mayores') {
                    if ($emisionHoy >= 45) {
                      $reportesMayores[] = $this->getReporteMayorDescargaModel($reporte);
                    }
                  } elseif ($tipoReporte == 'no') {
                    if ($emisionHoy < 45) {
                      $reportesMenores[] = $this->getReporteMenorDescargaModel($reporte);
                    }
                    if ($emisionHoy >= 45) {
                      $reportesMayores[] = $this->getReporteMayorDescargaModel($reporte);
                    }
                  }
              }
            }

            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();
            $date = date("Ymdhis");

            $sheet->setTitle('ReporteSemanal '.$date);

            $sheet->getCell('A1')->setValue('Reportes Mayores a 45 dias');

            $sheet->getCell('A2')->setValue('Ultima actualización');
            $sheet->getCell('B2')->setValue('Localidad');
            $sheet->getCell('C2')->setValue('Ref. Claim');
            $sheet->getCell('D2')->setValue('Transportista/Responsable');
            $sheet->getCell('E2')->setValue('Tipo de daño');
            $sheet->getCell('F2')->setValue('Monto reclamado USD');
            $sheet->getCell('G2')->setValue('Monto reclamado MXN');
            $sheet->getCell('H2')->setValue('Monto excedente de contrato MXN');
            $sheet->getCell('I2')->setValue('Monto estimado de recuperación MXN');
            $sheet->getCell('J2')->setValue('Monto rechazado MXN');
            $sheet->getCell('K2')->setValue('Monto aceptado MXN');
            $sheet->getCell('L2')->setValue('Monto cancelado MXN');
            $sheet->getCell('M2')->setValue('Flete no incluido en reclamo');
            $sheet->getCell('N2')->setValue('Fecha del evento');
            $sheet->getCell('O2')->setValue('Fecha de emisión');
            $sheet->getCell('P2')->setValue('Días del evento a la emisión');
            $sheet->getCell('Q2')->setValue('Días del evento a la fecha');
            $sheet->getCell('R2')->setValue('Año de Evento');
            $sheet->getCell('S2')->setValue('Año de Asignacion');
            $sheet->getCell('T2')->setValue('Año de Documentacion');
            $sheet->getCell('U2')->setValue('Fecha de escalación');
            $sheet->getCell('V2')->setValue('Área de escalación/ Responsable');
            $sheet->getCell('W2')->setValue('Fecha de resolución');
            $sheet->getCell('X2')->setValue('Días desde la escalación a la resolución');
            $sheet->getCell('Y2')->setValue('Estatus');
            $sheet->getCell('Z2')->setValue('Observaciones y sugerencias para agilizar la recuperación');

            $sheet->fromArray($reportesMayores, null, 'A3', true);
            $cantidadMayores = count($reportesMayores) + 6;
            $numero = strval($cantidadMayores);
            $sheet->getCell('A'.$numero)->setValue('Reportes Menores a 45 dias');
            $numero = strval($numero + 1);
            $sheet->getCell('A'.$numero)->setValue('Ultima actualización');
            $sheet->getCell('B'.$numero)->setValue('Localidad');
            $sheet->getCell('C'.$numero)->setValue('Ref. Claim');
            $sheet->getCell('D'.$numero)->setValue('Transportista/Responsable');
            $sheet->getCell('E'.$numero)->setValue('Tipo de daño');
            $sheet->getCell('F'.$numero)->setValue('Monto reclamado USD');
            $sheet->getCell('G'.$numero)->setValue('Monto reclamado MXN');
            $sheet->getCell('H'.$numero)->setValue('Monto excedente de contrato MXN');
            $sheet->getCell('I'.$numero)->setValue('Monto estimado de recuperación MXN');
            $sheet->getCell('J'.$numero)->setValue('Monto rechazado MXN');
            $sheet->getCell('K'.$numero)->setValue('Monto aceptado MXN');
            $sheet->getCell('L'.$numero)->setValue('Monto cancelado MXN');
            $sheet->getCell('M'.$numero)->setValue('Flete no incluido en reclamo');
            $sheet->getCell('N'.$numero)->setValue('Fecha del evento');
            $sheet->getCell('O'.$numero)->setValue('Fecha de emisión');
            $sheet->getCell('P'.$numero)->setValue('Días del evento a la emisión');
            $sheet->getCell('Q'.$numero)->setValue('Días del evento a la fecha');
            $sheet->getCell('R'.$numero)->setValue('Fecha de respuesta');
            $sheet->getCell('S'.$numero)->setValue('Fecha de resolución');
            $sheet->getCell('T'.$numero)->setValue('Fecha de solicitud de debito');
            $sheet->getCell('U'.$numero)->setValue('Fecha de aplicación de pago');
            $sheet->getCell('V'.$numero)->setValue('Estatus');
            $sheet->getCell('W'.$numero)->setValue('Observaciones y sugerencias para agilizar la recuperación');

            $numero = strval($numero + 1);
            $sheet->fromArray($reportesMenores, null, 'A'.$numero, true);

            $writer = new Xlsx($spreadsheet);

            $name = 'ReporteSemanal'.$date;
            $writer->save('build/'.$name.'.xlsx');
            $validation['enlace'] = $name.'.xlsx';
        }

        return $this->returnValidation($validation);
    }


    /**
     * @Route("/api/getExcelDocumentados/{email}/{token}", name="getExcelDocumentados", methods={ "GET"})
     *
     */
    public function getExcelDocumentados($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        if ($validation['status']) {

            $repository = $this->getDoctrine()->getRepository(Documentados::class);

            $documentados = $repository->findDocumentadosOrdered();

            $docs = [];
            foreach ($documentados as $documentado) {
                $docs[] = $this->getDocumentadosDescargaModel($documentado);
            }

            $datos = $docs;
            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('ReportesEnDocumentacion');

            $sheet->getCell('A1')->setValue('Referencia Claim');
            $sheet->getCell('B1')->setValue('Localidad');
            $sheet->getCell('C1')->setValue('Código de daño');
            $sheet->getCell('D1')->setValue('Planta');
            $sheet->getCell('E1')->setValue('Número de parte');
            $sheet->getCell('F1')->setValue('Cantidad de piezas');
            $sheet->getCell('G1')->setValue('Fecha de notificación RV');
            $sheet->getCell('H1')->setValue('Valor de perdida sin flete');
            $sheet->getCell('I1')->setValue('Valor de perdida con flete');
            $sheet->getCell('J1')->setValue('Documentación faltante');
            $sheet->getCell('K1')->setValue('Área y Responsable(s)');
            $sheet->getCell('L1')->setValue('Estatus');


            $sheet->fromArray($datos, null, 'A2', true);
            $writer = new Xlsx($spreadsheet);

            $date = date("Ymdhis");
            $name = 'ReportesDocumentados'.$date;
            $writer->save('build/'.$name.'.xlsx');
            $validation['enlace'] = $name.'.xlsx';
        }

        return $this->returnValidation($validation);
    }


    /**
     * @Route("/api/getExcelDocumentado/{email}/{token}/{id}", name="getExcelDocumentado", methods={ "GET"})
     *
     */
    public function getExcelDocumentado($email, $token,$id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        if ($validation['status']) {

            $repository = $this->getDoctrine()->getRepository(Documentados::class);

            $documentado = $repository->findOneBy([
              'id' => $id
            ]);

            $doc = '';
            $doc = $this->getDocumentadosDescargaModel($documentado);


            $datos = $doc;
            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('EnDocumentacion');

            $sheet->getCell('A1')->setValue('Referencia Claim');
            $sheet->getCell('B1')->setValue('Localidad');
            $sheet->getCell('C1')->setValue('Código de daño');
            $sheet->getCell('D1')->setValue('Planta');
            $sheet->getCell('E1')->setValue('Número de parte');
            $sheet->getCell('F1')->setValue('Cantidad de piezas');
            $sheet->getCell('G1')->setValue('Fecha de notificación RV');
            $sheet->getCell('H1')->setValue('Valor de perdida sin flete');
            $sheet->getCell('I1')->setValue('Valor de perdida con flete');
            $sheet->getCell('J1')->setValue('Documentación faltante');
            $sheet->getCell('K1')->setValue('Área y Responsable(s)');
            $sheet->getCell('L1')->setValue('Estatus');


            $sheet->fromArray($datos, null, 'A2', true);
            $writer = new Xlsx($spreadsheet);

            $date = date("Ymdhis");
            $name = 'ReporteDocumentados'.$documentado->getClaim().$date;
            $writer->save('build/'.$name.'.xlsx');
            $validation['enlace'] = $name.'.xlsx';
        }

        return $this->returnValidation($validation);
    }


    /**
     * @Route("/api/getExcelReporte/{email}/{token}/{reporte_id}", name="getExcelReporte", methods={ "GET"})
     *
     */
    public function getExcelReporte($email, $token, $reporte_id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Registro::class);

            $reportes = $repository->findBy([
              'id' => $reporte_id
            ]);

            $reports = [];
            $mayor = true;
            foreach ($reportes as $reporte) {

                $emisionHoy = 0;
                if ($reporte->getFechaEmision()) {
                    $interval = date_diff($reporte->getFechaEmision(), new \DateTime());
                    $emisionHoy = $interval->format('%a');
                }
                if ($emisionHoy < 45) {
                  $mayor = false;
                  $reports[] = $this->getReporteMenorDescargaModel($reporte);
                }else {
                  $reports[] = $this->getReporteMayorDescargaModel($reporte);

                }
            }

            $datos = $reports;
            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('Reporte-'.$reports[0][2]);

            if ($mayor) {
              $sheet->getCell('A1')->setValue('Reporte Mayores a 45 dias');

              $sheet->getCell('A2')->setValue('Ultima actualización');
              $sheet->getCell('B2')->setValue('Localidad');
              $sheet->getCell('C2')->setValue('Ref. Claim');
              $sheet->getCell('D2')->setValue('Transportista/Responsable');
              $sheet->getCell('E2')->setValue('Tipo de daño');
              $sheet->getCell('F2')->setValue('Monto reclamado USD');
              $sheet->getCell('G2')->setValue('Monto reclamado MXN');
              $sheet->getCell('H2')->setValue('Monto excedente de contrato MXN');
              $sheet->getCell('I2')->setValue('Monto estimado de recuperación MXN');
              $sheet->getCell('J2')->setValue('Monto rechazado MXN');
              $sheet->getCell('K2')->setValue('Monto aceptado MXN');
              $sheet->getCell('L2')->setValue('Monto cancelado MXN');
              $sheet->getCell('M2')->setValue('Flete no incluido en reclamo');
              $sheet->getCell('N2')->setValue('Fecha del evento');
              $sheet->getCell('O2')->setValue('Fecha de emisión');
              $sheet->getCell('P2')->setValue('Días del evento a la emisión');
              $sheet->getCell('Q2')->setValue('Días del evento a la fecha');
              $sheet->getCell('R2')->setValue('Fecha de 1er notificación a RM:');
              $sheet->getCell('S2')->setValue('Fecha de 2da notificación a RM');
              $sheet->getCell('T2')->setValue('Fecha de 3ra notificación a RM');
              $sheet->getCell('U2')->setValue('Fecha de escalación');
              $sheet->getCell('V2')->setValue('Área de escalación/ Responsable');
              $sheet->getCell('W2')->setValue('Fecha de resolución');
              $sheet->getCell('X2')->setValue('Días desde la escalación a la resolución');
              $sheet->getCell('Y2')->setValue('Estatus');
              $sheet->getCell('Z2')->setValue('Observaciones y sugerencias para agilizar la recuperación');

              $sheet->fromArray($datos, null, 'A3', true);

            }else {
              $numero = strval(1);
              $sheet->getCell('A'.$numero)->setValue('Reporte Menores a 45 dias');
              $numero = strval(2);
              $sheet->getCell('A'.$numero)->setValue('Ultima actualización');
              $sheet->getCell('B'.$numero)->setValue('Localidad');
              $sheet->getCell('C'.$numero)->setValue('Ref. Claim');
              $sheet->getCell('D'.$numero)->setValue('Transportista/Responsable');
              $sheet->getCell('E'.$numero)->setValue('Tipo de daño');
              $sheet->getCell('F'.$numero)->setValue('Monto reclamado USD');
              $sheet->getCell('G'.$numero)->setValue('Monto reclamado MXN');
              $sheet->getCell('H'.$numero)->setValue('Monto excedente de contrato MXN');
              $sheet->getCell('I'.$numero)->setValue('Monto estimado de recuperación MXN');
              $sheet->getCell('J'.$numero)->setValue('Monto rechazado MXN');
              $sheet->getCell('K'.$numero)->setValue('Monto aceptado MXN');
              $sheet->getCell('L'.$numero)->setValue('Monto cancelado MXN');
              $sheet->getCell('M'.$numero)->setValue('Flete no incluido en reclamo');
              $sheet->getCell('N'.$numero)->setValue('Fecha del evento');
              $sheet->getCell('O'.$numero)->setValue('Fecha de emisión');
              $sheet->getCell('P'.$numero)->setValue('Días del evento a la emisión');
              $sheet->getCell('Q'.$numero)->setValue('Días del evento a la fecha');
              $sheet->getCell('R'.$numero)->setValue('Fecha de respuesta');
              $sheet->getCell('S'.$numero)->setValue('Fecha de resolución');
              $sheet->getCell('T'.$numero)->setValue('Fecha de solicitud de debito');
              $sheet->getCell('U'.$numero)->setValue('Fecha de aplicación de pago');
              $sheet->getCell('V'.$numero)->setValue('Estatus');
              $sheet->getCell('W'.$numero)->setValue('Observaciones y sugerencias para agilizar la recuperación');

              $numero = strval(3);
              $sheet->fromArray($datos, null, 'A'.$numero, true);
            }

            $writer = new Xlsx($spreadsheet);

            $date = date("Ymdhis");
            $name = 'Reporte-'.$reports[0][2];
            $writer->save('build/'.$name.'.xlsx');
            $validation['enlace'] = $name.'.xlsx';
        }

        return $this->returnValidation($validation);
    }


    public function getReporteDescargaModel($siniestro)
    {
        $model = $this->getReporteModelView($siniestro);
        return [
          $model->actualizacion,
          $model->localidad,
          $model->claim,
          $model->transportista,
          $model->tipo,
          $model->reclamadoUSD,
          $model->reclamadoMXN,
          $model->excedente,
          $model->estimado,
          $model->rechazado,
          $model->aceptado,
          $model->cancelado,
          $model->flete,
          $model->fechaEvento,
          $model->fechaEmision,
          $model->eventoEmision,
          $model->emisionHoy,
          $model->fechaRespuesta,
          $model->fechaSolicitud,
          $model->fechaAplicacion,
          $model->fechaEscalacion,
          $model->area,
          $model->fechaResolucion,
          $model->escalacionResolucion,
          $model->estatus,
          $model->observaciones,
          $model->anoEvento,
          $model->anoAsignacion,
          $model->anoDocumentacion,
          $model->formaPago,

        ];
    }

    public function getReporteMayorDescargaModel($siniestro)
    {
        $model = $this->getReporteModelView($siniestro);
        return [
          $model->actualizacion,
          $model->localidad,
          $model->claim,
          $model->transportista,
          $model->tipo,
          $model->reclamadoUSD,
          $model->reclamadoMXN,
          $model->excedente,
          $model->estimado,
          $model->rechazado,
          $model->aceptado,
          $model->cancelado,
          $model->flete,
          $model->fechaEvento,
          $model->fechaEmision,
          $model->eventoEmision,
          $model->emisionHoy,
          $model->fechaEscalacion,
          $model->area,
          $model->fechaResolucion,
          $model->escalacionResolucion,
          $model->estatus,
          $model->observaciones,
          $model->anoEvento,
          $model->anoAsignacion,
          $model->anoDocumentacion,
          $model->formaPago,
        ];
    }


    public function getReporteMenorDescargaModel($siniestro)
    {
        $model = $this->getReporteModelView($siniestro);
        return [
          $model->actualizacion,
          $model->localidad,
          $model->claim,
          $model->transportista,
          $model->tipo,
          $model->reclamadoUSD,
          $model->reclamadoMXN,
          $model->excedente,
          $model->estimado,
          $model->rechazado,
          $model->aceptado,
          $model->cancelado,
          $model->flete,
          $model->fechaEvento,
          $model->fechaEmision,
          $model->eventoEmision,
          $model->emisionHoy,
          $model->fechaRespuesta,
          $model->fechaResolucion,
          $model->fechaSolicitud,
          $model->fechaAplicacion,
          $model->estatus,
          $model->observaciones,
        ];
    }

    public function getDocumentadosDescargaModel($documentado)
    {
        $model = $this->getDocumentadosModel($documentado);
        return [
          $model->claim,
          $model->localidad,
          $model->codigo,
          $model->planta,
          $model->numero,
          $model->cantidad,
          $model->fecha,
          $model->perdidaSinFlete,
          $model->perdidaConFlete,
          $model->documentacionFaltante,
          $model->area,
          $model->estatus,
        ];
    }

    public function getReporteModel($reporte)
    {
        $model = new ReporteFullModel();
        $model->id = $reporte->getId();
        if ($reporte->getActualizacion()) {
            $model->actualizacion = $reporte->getActualizacion()->format('Y-m-d');
        } else {
            $model->actualizacion = '';
        }
        if ($reporte->getLocalidad()) {
            $model->localidad = $reporte->getLocalidad()->getLocalidad();
        } else {
            $model->localidad = '';
        }
        if ($reporte->getTransportista()) {
            $model->transportista = $reporte->getTransportista()->getTransportista();
        } else {
            $model->transportista = '';
        }
        $model->claim= $reporte->getClaim();
        $model->tipo= $reporte->getTipo();
        $model->reclamadoUSD= $reporte->getReclamadoUSD();
        $model->reclamadoMXN= $reporte->getReclamadoMXN();
        $model->excedente= $reporte->getExcedenteMXN();
        $model->estimado= $reporte->getEstimadoMXN();
        $model->rechazado= $reporte->getRechazadoMXN();
        $model->aceptado= $reporte->getAceptadoMXN();
        $model->cancelado= $reporte->getCanceladoMXN();
        $model->flete= $reporte->getFlete();
        if ($reporte->getFechaEvento()) {
            $model->fechaEvento = $reporte->getFechaEvento()->format('Y-m-d');
        } else {
            $model->fechaEvento = '';
        }
        if ($reporte->getFechaEmision()) {
            $model->fechaEmision = $reporte->getFechaEmision()->format('Y-m-d');
        } else {
            $model->fechaEmision = '';
        }
        
        if ($reporte->getFechaRespuesta()) {
            $model->fechaRespuesta = $reporte->getFechaRespuesta()->format('Y-m-d');
        } else {
            $model->fechaRespuesta = '';
        }
        if ($reporte->getFechaSolicitud()) {
            $model->fechaSolicitud = $reporte->getFechaSolicitud()->format('Y-m-d');
        } else {
            $model->fechaSolicitud = '';
        }
        if ($reporte->getFechaAplicacion()) {
            $model->fechaAplicacion = $reporte->getFechaAplicacion()->format('Y-m-d');
        } else {
            $model->fechaAplicacion = '';
        }

        if ($reporte->getFechaEscalacion()) {
            $model->fechaEscalacion = $reporte->getFechaEscalacion()->format('Y-m-d');
        } else {
            $model->fechaEscalacion = '';
        }

        if ($reporte->getFechaResolucion()) {
            $model->fechaResolucion = $reporte->getFechaResolucion()->format('Y-m-d');
        } else {
            $model->fechaResolucion = '';
        }

        $model->area= $reporte->getArea();
        $model->estatus= $reporte->getEstatus();
        $model->observaciones= $reporte->getObservaciones();
        if ($reporte->getFechaEvento() && $reporte->getFechaEmision()) {
            $interval = date_diff($reporte->getFechaEvento(), $reporte->getFechaEmision());
            $model->eventoEmision = $interval->format('%a');
        } else {
            $model->eventoEmision = '';
        }
        if ($reporte->getFechaEmision()) {
            $interval = date_diff($reporte->getFechaEmision(), new \DateTime());
            $model->emisionHoy = $interval->format('%a');
        } else {
            $model->emisionHoy = '';
        }
        if ($reporte->getFechaEscalacion() && $reporte->getFechaResolucion()) {
            $interval = date_diff($reporte->getFechaEscalacion(), $reporte->getFechaResolucion());
            $model->escalacionResolucion = $interval->format('%a');
        } else {
            $model->escalacionResolucion = '';
        }
        $model->anoEvento= $reporte->getAnoEvento();
        $model->anoAsignacion= $reporte->getAnoAsignacion();
        $model->anoDocumentacion= $reporte->getAnoDocumentacion();
        $model->formaPago= $reporte->getFormaPAgo();



        return $model;
    }


    public function getReporteModelView($reporte)
    {
        $model = new ReporteFullModelView();
        $model->id = $reporte->getId();
        if ($reporte->getActualizacion()) {
            $model->actualizacion = $reporte->getActualizacion()->format('Y-m-d');
        } else {
            $model->actualizacion = '';
        }
        if ($reporte->getLocalidad()) {
            $model->localidad = $reporte->getLocalidad()->getLocalidad();
        } else {
            $model->localidad = '';
        }
        if ($reporte->getTransportista()) {
            $model->transportista = $reporte->getTransportista()->getTransportista();
        } else {
            $model->transportista = '';
        }
        if ($reporte->getArea()) {
            $model->area = $reporte->getArea()->getArea();
        } else {
            $model->area = '';
        }
        $model->claim= $reporte->getReferencia();
        $model->tipo= $reporte->getTipo();
        $model->reclamadoUSD= $reporte->getReclamadoUSD();
        $model->reclamadoMXN= $reporte->getReclamadoMXN();
        $model->excedente= $reporte->getExcedente();
        $model->estimado= $reporte->getEstimado();
        $model->rechazado= $reporte->getExcedente();
        $model->aceptado= $reporte->getAceptado();
        $model->cancelado= $reporte->getCancelado();
        $model->flete= $reporte->getFlete();
        if ($reporte->getFechaEvento()) {
            $model->fechaEvento = $reporte->getFechaEvento()->format('Y-m-d');
        } else {
            $model->fechaEvento = '';
        }
        if ($reporte->getFechaEmision()) {
            $model->fechaEmision = $reporte->getFechaEmision()->format('Y-m-d');
        } else {
            $model->fechaEmision = '';
        }
        if ($reporte->getFechaRespuesta()) {
            $model->fechaRespuesta = $reporte->getFechaRespuesta()->format('Y-m-d');
        } else {
            $model->fechaRespuesta = '';
        }
        if ($reporte->getFechaCheque()) {
            $model->fechaSolicitud = $reporte->getFechaCheque()->format('Y-m-d');
        } else {
            $model->fechaSolicitud = '';
        }
        if ($reporte->getFechaAplicacion()) {
            $model->fechaAplicacion = $reporte->getFechaAplicacion()->format('Y-m-d');
        } else {
            $model->fechaAplicacion = '';
        }

        if ($reporte->getFechaEscalacion()) {
            $model->fechaEscalacion = $reporte->getFechaEscalacion()->format('Y-m-d');
        } else {
            $model->fechaEscalacion = '';
        }

        if ($reporte->getFechaResolucion()) {
            $model->fechaResolucion = $reporte->getFechaResolucion()->format('Y-m-d');
        } else {
            $model->fechaResolucion = '';
        }


        $model->estatus= $reporte->getEstatus();
        $model->observaciones= $reporte->getObservaciones();
        if ($reporte->getFechaEvento() && $reporte->getFechaEmision()) {
            $interval = date_diff($reporte->getFechaEvento(), $reporte->getFechaEmision());
            $model->eventoEmision = $interval->format('%a');
        } else {
            $model->eventoEmision = '';
        }
        if ($reporte->getFechaEmision()) {
            $interval = date_diff($reporte->getFechaEmision(), new \DateTime());
            $model->emisionHoy = $interval->format('%a');
        } else {
            $model->emisionHoy = '';
        }
        if ($reporte->getFechaEscalacion() && $reporte->getFechaResolucion()) {
            $interval = date_diff($reporte->getFechaEscalacion(), $reporte->getFechaResolucion());
            $model->escalacionResolucion = $interval->format('%a');
        } else {
            $model->escalacionResolucion = '';
        }

        if ($model->emisionHoy) {
          $model->mayor45= 1;
        }else {
          $model->mayor45= 0;
        }

        $model->anoEvento= $reporte->getAnoEvento();
        $model->anoAsignacion= $reporte->getAnoAsignacion();
        $model->anoDocumentacion= $reporte->getAnoDocumentacion();
        $model->formaPago= $reporte->getFormaPAgo();

        return $model;
    }


    /**
     * @Route("/api/getReporte/{email}/{token}/{reporte_id}", name="getReporte", methods={"GET"})
     *
     */
    public function getReporte($email, $token, $reporte_id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR','ROLE_CAPTURISTA', 'ROLE_PERSONAL']);
        $modelo = '';
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Registro::class);

            $reporte = $repository->findOneBy([
              'id'=> $reporte_id
            ]);

            if ($reporte) {
                $modelo = $this->getReporteModelView($reporte);
                $status = true;
                $errorCode = 0;
                $errorMsg = "Sin Error";
                $validation['item'] = $modelo;
            } else {
                $status = false;
                $errorCode = 1;
                $errorMsg = "No se encontro el reporte";
            }
        } else {
            $status = false;
            $errorCode = 2;
            $errorMsg = "Fallo la validacion";
        }
        $validation['status'] = $status;
        $validation['error']['code'] = $errorCode;
        $validation['error']['mensaje'] = $errorMsg;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getDocumentado/{email}/{token}/{reporte_id}", name="getDocumentado", methods={"GET"})
     *
     */
    public function getDocumentado($email, $token, $reporte_id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR','ROLE_CAPTURISTA', 'ROLE_PERSONAL']);
        $modelo = '';
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Documentados::class);

            $reporte = $repository->findOneBy([
              'id'=> $reporte_id
            ]);

            if ($reporte) {
                $modelo = $this->getDocumentadosModelView($reporte);
                $status = true;
                $errorCode = 0;
                $errorMsg = "Sin Error";
                $validation['item'] = $modelo;
            } else {
                $status = false;
                $errorCode = 1;
                $errorMsg = "No se encontro el reporte";
            }
        } else {
            $status = false;
            $errorCode = 2;
            $errorMsg = "Fallo la validacion";
        }
        $validation['status'] = $status;
        $validation['error']['code'] = $errorCode;
        $validation['error']['mensaje'] = $errorMsg;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getTransportistas/{email}/{token}", name="getTransportistas", methods={"GET"})
     *
     */
    public function getTransportistas($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Transportista::class);
            $transportistas = $repository->findBy(
                [],
                ['transportista' => 'ASC']
            );
            foreach ($transportistas as $transportista) {
                $models[] = $this->getTransportistaModel($transportista);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getLocalidades/{email}/{token}", name="getLocalidades", methods={"GET"})
     *
     */
    public function getLocalidades($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Localidad::class);
            $localidades = $repository->findBy(
                [],
                ['localidad' => 'ASC']
            );
            foreach ($localidades as $localidad) {
                $models[] = $this->getLocalidadModel($localidad);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }
    /**
     * @Route("/api/getRutas/{email}/{token}", name="getRutas", methods={"GET"})
     *
     */
    public function getRutas($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Ruta::class);
            $rutas = $repository->findBy(
                [],
                ['ruta' => 'ASC']
            );
            foreach ($rutas as $ruta) {
                $models[] = $this->getRutaModel($ruta);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getPlantas/{email}/{token}", name="getPlantas", methods={"GET"})
     *
     */
    public function getPlantas($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Planta::class);
            $plantas = $repository->findBy(
                [],
                ['planta' => 'ASC']
            );
            foreach ($plantas as $planta) {
                $models[] = $this->getPlantaModel($planta);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getAreas/{email}/{token}", name="getAreas", methods={"GET"})
     *
     */
    public function getAreas($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Area::class);
            $areas = $repository->findBy(
                [],
                ['area' => 'ASC']
            );
            foreach ($areas as $area) {
                $models[] = $this->getAreaModel($area);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getProveedores/{email}/{token}", name="getProveedores", methods={"GET"})
     *
     */
    public function getProveedores($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Proveedor::class);
            $proveedores = $repository->findBy(
                [],
                ['proveedor' => 'ASC']
            );
            foreach ($proveedores as $proveedor) {
                $models[] = $this->getProveedorModel($proveedor);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getTipos/{email}/{token}", name="getTipos", methods={"GET"})
     *
     */
    public function getTipos($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Registro::class);
            $tipos = $repository->findTipos();
            foreach ($tipos as $tipo) {
                $models[] = $this->getTipoModel($tipo);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getDescripciones/{email}/{token}", name="getDescripciones", methods={"GET"})
     *
     */
    public function getDescripciones($email, $token)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $models = [];
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Registro::class);
            $descripciones = $repository->findDescripciones();
            foreach ($descripciones as $descripcion) {
                $models[] = $this->getDescripcionModel($descripcion);
            }
        }
        $validation['items']= $models;
        return $this->returnValidation($validation);
    }

    public function getTransportistaModel($transportista)
    {
        $model = new TransportistaModel();
        $model->transportista = $transportista->getTransportista();
        return $model;
    }
    public function getDescripcionModel($descripcion)
    {
        $model = new DescripcionModel();
        $model->descripcion = $descripcion['descripcion'];
        return $model;
    }
    public function getTipoModel($tipo)
    {
        $model = new TipoModel();
        $model->tipo = $tipo['tipo'];
        return $model;
    }

    public function getLocalidadModel($localidad)
    {
        $model = new LocalidadModel();
        $model->localidad = $localidad->getLocalidad();
        return $model;
    }

    public function getRutaModel($ruta)
    {
        $model = new RutaModel();
        $model->ruta = $ruta->getRuta();
        return $model;
    }

    public function getPlantaModel($planta)
    {
        $model = new PlantaModel();
        $model->planta = $planta->getPlanta();
        return $model;
    }

    public function getAreaModel($area)
    {
        $model = new AreaModel();
        $model->area = $area->getArea();
        return $model;
    }

    public function getProveedorModel($proveedor)
    {
        $model = new ProveedorModel();
        $model->proveedor = $proveedor->getProveedor();
        return $model;
    }
 
    /**
     * @Route("/api/newRegistro/{email}/{token}", name="newRegistro", methods={"POST"})
     *
     */
    public function newRegistro($email, $token, Request $request)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA']);

        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);
            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }

            $form = $this->createForm(RegistroType::class, null, [
          'csrf_protection' => false,
            ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
              'errors' => $errors
          ], 400);
            }
            $localidad =   $form->get('localidad')->getData();
            $planta =   $form->get('planta')->getData();
            $tipo =   $form->get('tipo')->getData();
            $descripcion =   $form->get('descripcion')->getData();
            $transportista =   $form->get('transportista')->getData();
            $referencia =   $form->get('referencia')->getData();
            $reclamadoUSD =   $form->get('reclamadoUSD')->getData();
            $reclamadoMXN =   $form->get('reclamadoMXN')->getData();
            $aceptado =   $form->get('aceptado')->getData();
            $recuperado =   $form->get('recuperado')->getData();

            $ajustes =   $form->get('ajustes')->getData();
            $reclamoDocumentacion =   $form->get('reclamoDocumentacion')->getData();
            $reclamoProceso =   $form->get('reclamoProceso')->getData();
            $ajuste =   $form->get('ajuste')->getData();
            $cancelado =   $form->get('cancelado')->getData();
            $flete =   $form->get('flete')->getData();
            $menores =   $form->get('menores')->getData();
            $excedente =   $form->get('excedente')->getData();
            $estimado =   $form->get('estimado')->getData();
            $fechaEvento =   $form->get('fechaEvento')->getData();
            $fechaAsignacion =   $form->get('fechaAsignacion')->getData();
            $fechaDocumentacion =   $form->get('fechaDocumentacion')->getData();
            $fechaEmision =   $form->get('fechaEmision')->getData();
            $fechaRespuesta =   $form->get('fechaRespuesta')->getData();
            $fechaAviso =   $form->get('fechaAviso')->getData();
            $fechaAplicacion =   $form->get('fechaAplicacion')->getData();

            $estatus =   $form->get('estatus')->getData();
            $tipoMaterial =   $form->get('tipoMaterial')->getData();
            $escalado =   $form->get('escalado')->getData();
            $area =   $form->get('area')->getData();
            $proveedor =   $form->get('proveedor')->getData();

            $fechaEscalacion =   $form->get('fechaEscalacion')->getData();
            $fechaResolucion =   $form->get('fechaResolucion')->getData();
            $ruta =   $form->get('ruta')->getData();
            $caja =   $form->get('caja')->getData();
            $comentarios =   $form->get('comentarios')->getData();
            $observaciones =   $form->get('observaciones')->getData();
            
            #Campos agregados en abril 2023
            $anoEvento =   $form->get('anoEvento')->getData();
            $anoAsignacion =   $form->get('anoAsignacion')->getData();
            $anoDocumentacion =   $form->get('anoDocumentacion')->getData();
            $formaPago =   $form->get('formaPago')->getData();

    
            $status =false;
            $validation = [
            'status' => $status
            ];

            if (is_null($referencia)) {
                $status = false;
                $errorCode = 4;
                $errorMsg = "Completa los campos";
            } else {
                $registro = new Registro();

                $createdDate = new \DateTime();
                $registro->setActualizacion($createdDate);


                if ($localidad) {
                    $localidadRepository = $this->getDoctrine()->getRepository(Localidad::class);
                    $local = $localidadRepository->findOneBy([
                      'localidad' => $localidad
                    ]);
                    if ($local) {
                        $registro->setLocalidad($local);
                    } else {
                        $newLocalidad = new Localidad();
                        $newLocalidad ->setLocalidad($localidad);
                        $this->manager->persist($newLocalidad);
                        $registro->setLocalidad($newLocalidad);
                    }
                } else {
                    $registro->setLocalidad(null);
                }


                if ($transportista) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Transportista::class);
                    $trans = $transportistaRepository->findOneBy([
                    'transportista' => $transportista
                  ]);
                    if ($trans) {
                        $registro->setTransportista($trans);
                    } else {
                        $newTranspor = new Transportista();
                        $newTranspor ->setTransportista($transportista);
                        $this->manager->persist($newTranspor);
                        $registro->setTransportista($newTranspor);
                    }
                } else {
                    $registro->setTransportista(null);
                }

                if ($planta) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Planta::class);
                    $plant = $transportistaRepository->findOneBy([
                    'planta' => $planta
                  ]);
                    if ($plant) {
                        $registro->setPlanta($plant);
                    } else {
                        $newPlant = new Planta();
                        $newPlant ->setPlanta($planta);
                        $newPlant ->setLocalidad($registro->getLocalidad());
                        $this->manager->persist($newPlant);
                        $registro->setPlanta($newPlant);
                    }
                } else {
                    $registro->setPlanta(null);
                }

                if ($area) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Area::class);
                    $ar = $transportistaRepository->findOneBy([
                    'area' => $area
                  ]);
                    if ($ar) {
                        $registro->setArea($ar);
                    } else {
                        $newArea = new Area();
                        $newArea ->setArea($area);
                        $this->manager->persist($newArea);

                        $registro->setArea($newArea);
                    }
                } else {
                    $registro->setArea(null);
                }

                if ($ruta) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Ruta::class);
                    $ar = $transportistaRepository->findOneBy([
                    'ruta' => $ruta
                  ]);
                    if ($ar) {
                        $registro->setRuta($ar);
                    } else {
                        $newRuta = new Ruta();
                        $newRuta ->setRuta($ruta);
                        $this->manager->persist($newRuta);
                        $registro->setRuta($newRuta);
                    }
                } else {
                    $registro->setRuta(null);
                }

                if ($proveedor) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Proveedor::class);
                    $ar = $transportistaRepository->findOneBy([
                    'proveedor' => $proveedor
                  ]);
                    if ($ar) {
                        $registro->setProveedor($ar);
                    } else {
                        $newProv = new Proveedor();
                        $newProv ->setProveedor($proveedor);
                        $this->manager->persist($newProv);

                        $registro->setProveedor($newProv);
                    }
                } else {
                    $registro->setProveedor(null);
                }

                $registro->setRecuperado($recuperado);

                $registro->setTipo($tipo);
                $registro->setDescripcion($descripcion);
                $registro->setReferencia($referencia);
                $registro->setReclamadoUSD($reclamadoUSD);
                $registro->setReclamadoMXN($reclamadoMXN);
                $registro->setAceptado($aceptado);
                $registro->setAjustes($ajustes);
                $registro->setReclamoDocumentacion($reclamoDocumentacion);
                $registro->setReclamoProceso($reclamoProceso);
                $registro->setAjuste($ajuste);
                $registro->setCancelado($cancelado);

                $registro->setFlete($flete);
                $registro->setMenores($menores);
                $registro->setExcedente($excedente);
                $registro->setEstimado($estimado);

                if (is_null($fechaEvento) || $fechaEvento == '') {
                    $registro->setFechaEvento(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaEvento);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaEvento($fecha);
                }

                if (is_null($fechaAsignacion) || $fechaAsignacion == '') {
                    $registro->setFechaAsignacion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaAsignacion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaAsignacion($fecha);
                }

                if (is_null($fechaDocumentacion)|| $fechaDocumentacion == '') {
                    $registro->setFechaDocumentacion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaDocumentacion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaDocumentacion($fecha);
                }

                if (is_null($fechaEmision)|| $fechaEmision == '') {
                    $registro->setFechaEmision(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaEmision);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaEmision($fecha);
                }

                if (is_null($fechaRespuesta)|| $fechaRespuesta == '') {
                    $registro->setFechaRespuesta(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaRespuesta);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaRespuesta($fecha);
                }

                if (is_null($fechaAviso)|| $fechaAviso == '') {
                    $registro->setFechaAviso(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaAviso);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaAviso($fecha);
                }

                if (is_null($fechaAplicacion)|| $fechaAplicacion == '') {
                    $registro->setFechaAplicacion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaAplicacion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaAplicacion($fecha);
                }

                if (is_null($fechaEscalacion)|| $fechaEscalacion == '') {
                    $registro->setFechaEscalacion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaEscalacion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaEscalacion($fecha);
                }

                if (is_null($fechaResolucion)|| $fechaResolucion == '') {
                    $registro->setFechaResolucion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaResolucion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaResolucion($fecha);
                }

                //Campos agregadoes en abril 2023

                $registro->setAnoEvento($anoEvento);
                
                $registro->setAnoAsignacion($anoAsignacion);

                $registro->setAnoDocumentacion($anoDocumentacion);

                $registro->setFormaPago($formaPago);

                
                ////

                $registro->setEstatus($estatus);
                $registro->setTipoMaterial($tipoMaterial);
                $registro->setEscalado($escalado);
                $registro->setCaja($caja);
                $registro->setComentarios($comentarios);
                $registro->setObservaciones($observaciones);



                $this->manager->persist($registro);
                $this->manager->flush();
                $status = true;
                $errorCode = 0;
                $errorMsg = "RegistroGuardado";
            }


            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/uploadFile/{email}/{token}", name="uploadFile", methods={ "POST"})
     *
     */
    public function uploadFile($email, $token, Request $request)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA']);
        if ($validation['status']) {
            $currentFile = $request->files->get('currentFile');
            $date = date("Ymdhis");
            $name = 'datosUploads'.$date.'.xlsx';
            $someNewFilename = $name;

            if ($currentFile) {
                try {
                    $currentFile->move(
                        'build/',
                        $someNewFilename
                    );

                    $filePath = "build/".$someNewFilename;
                    $reader = ReaderEntityFactory::createReaderFromFile($filePath);
                    $reader->setShouldFormatDates(true);
                    $reader->open($filePath);

                    foreach ($reader->getSheetIterator() as $sheet) {
                        $r = 0;
                        foreach ($sheet->getRowIterator() as $row) {
                            if ($r > 0) {
                                $cells = $row->getCells();
                                $localidad =  $cells[0];
                                $planta =  $cells[1];
                                $tipo = $cells[2];
                                $descripcion = $cells[3];
                                $transportista = $cells[4];
                                $referencia = $cells[5];
                                $reclamadoUSD = $cells[6];
                                $reclamadoMXN = $cells[7];
                                $aceptado = $cells[8];
                                $recuperado = $cells[9];
                                $ajustes = $cells[10];
                                $reclamoDocumentacion = $cells[11];
                                $reclamoProceso =$cells[12];
                                $ajuste = $cells[13];
                                $cancelado = $cells[14];
                                $flete = $cells[15];
                                $menores = $cells[16];
                                $excedente = $cells[17];
                                $estimado = $cells[18];
                                $fechaEvento = $cells[19];
                                $fechaAsignacion = $cells[20];
                                $fechaDocumentacion = $cells[22];
                                $fechaEmision =$cells[24];
                                $fechaRespuesta = $cells[26];
                                $fechaAviso = $cells[28];
                                $fechaAplicacion = $cells[29];
                                $estatus =$cells[31];
                                $comentarios = $cells[32];
                                $actualizacion =$cells[33];
                                $observaciones = $cells[34];
                                $tipoMaterial =$cells[35];
                                $escalado = $cells[36];
                                $area =$cells[37];
                                $fechaEscalacion = $cells[38];
                                $fechaResolucion = $cells[39];
                                $proveedor = $cells[40];
                                $ruta = $cells[41];
                                $caja =$cells[42];
                                $anoEvento =$cells[43];
                                $anoAsignacion = $cells[44];
                                $anoDocumentacion = $cells[45];
                                $formaPago = $cells[46];


                                $repository = $this->getDoctrine()->getRepository(Registro::class);
                                $registro = $repository->findOneBy([
                                  'referencia' => $referencia,
                                ]);
                                if ($registro) {
                                    // Do nothing
                                } else {
                                    $registro = new Registro();

                                    $createdDate = new \DateTime();
                                    $registro->setActualizacion($createdDate);

                                    if ($localidad) {
                                        $localidadRepository = $this->getDoctrine()->getRepository(Localidad::class);
                                        $local = $localidadRepository->findOneBy([
                                        'localidad' => $localidad
                                      ]);
                                        if ($local) {
                                            $registro->setLocalidad($local);
                                        } else {
                                            $newLocalidad = new Localidad();
                                            $newLocalidad ->setLocalidad($localidad);
                                            $this->manager->persist($newLocalidad);
                                            $registro->setLocalidad($newLocalidad);
                                        }
                                    } else {
                                        $registro->setLocalidad(null);
                                    }


                                    if ($transportista) {
                                        $transportistaRepository = $this->getDoctrine()->getRepository(Transportista::class);
                                        $trans = $transportistaRepository->findOneBy([
                                      'transportista' => $transportista
                                    ]);
                                        if ($trans) {
                                            $registro->setTransportista($trans);
                                        } else {
                                            $newTranspor = new Transportista();
                                            $newTranspor ->setTransportista($transportista);
                                            $this->manager->persist($newTranspor);
                                            $registro->setTransportista($newTranspor);
                                        }
                                    } else {
                                        $registro->setTransportista(null);
                                    }

                                    if ($planta) {
                                        $transportistaRepository = $this->getDoctrine()->getRepository(Planta::class);
                                        $plant = $transportistaRepository->findOneBy([
                                      'planta' => $planta
                                    ]);
                                        if ($plant) {
                                            $registro->setPlanta($plant);
                                        } else {
                                            $newPlant = new Planta();
                                            $newPlant ->setPlanta($planta);
                                            $newPlant ->setLocalidad($registro->getLocalidad());
                                            $this->manager->persist($newPlant);
                                            $registro->setPlanta($newPlant);
                                        }
                                    } else {
                                        $registro->setPlanta(null);
                                    }

                                    if ($area) {
                                        $transportistaRepository = $this->getDoctrine()->getRepository(Area::class);
                                        $ar = $transportistaRepository->findOneBy([
                                      'area' => $area
                                    ]);
                                        if ($ar) {
                                            $registro->setArea($ar);
                                        } else {
                                            $newArea = new Area();
                                            $newArea ->setArea($area);
                                            $this->manager->persist($newArea);

                                            $registro->setArea($newArea);
                                        }
                                    } else {
                                        $registro->setArea(null);
                                    }

                                    if ($ruta) {
                                        $transportistaRepository = $this->getDoctrine()->getRepository(Ruta::class);
                                        $ar = $transportistaRepository->findOneBy([
                                      'ruta' => $ruta
                                    ]);
                                        if ($ar) {
                                            $registro->setRuta($ar);
                                        } else {
                                            $newRuta = new Ruta();
                                            $newRuta ->setRuta($ruta);
                                            $this->manager->persist($newRuta);
                                            $registro->setRuta($newRuta);
                                        }
                                    } else {
                                        $registro->setRuta(null);
                                    }

                                    if ($proveedor) {
                                        $transportistaRepository = $this->getDoctrine()->getRepository(Proveedor::class);
                                        $ar = $transportistaRepository->findOneBy([
                                      'proveedor' => $proveedor
                                    ]);
                                        if ($ar) {
                                            $registro->setProveedor($ar);
                                        } else {
                                            $newProv = new Proveedor();
                                            $newProv ->setProveedor($proveedor);
                                            $this->manager->persist($newProv);

                                            $registro->setProveedor($newProv);
                                        }
                                    } else {
                                        $registro->setProveedor(null);
                                    }

                                    $registro->setRecuperado($recuperado);

                                    $registro->setTipo($tipo);
                                    $registro->setDescripcion($descripcion);
                                    $registro->setReferencia($referencia);
                                    $registro->setReclamadoUSD($reclamadoUSD);
                                    $registro->setReclamadoMXN($reclamadoMXN);
                                    $registro->setAceptado($aceptado);
                                    $registro->setAjustes($ajustes);
                                    $registro->setReclamoDocumentacion($reclamoDocumentacion);
                                    $registro->setReclamoProceso($reclamoProceso);
                                    $registro->setAjuste($ajuste);
                                    $registro->setCancelado($cancelado);

                                    $registro->setFlete($flete);
                                    $registro->setMenores($menores);
                                    $registro->setExcedente($excedente);
                                    $registro->setEstimado($estimado);

                                    if (is_null($fechaEvento) || $fechaEvento == '') {
                                        $registro->setFechaEvento(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaEvento);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaEvento($fecha);
                                    }

                                    if (is_null($fechaAsignacion) || $fechaAsignacion == '') {
                                        $registro->setFechaAsignacion(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaAsignacion);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaAsignacion($fecha);
                                    }

                                    if (is_null($fechaDocumentacion)|| $fechaDocumentacion == '') {
                                        $registro->setFechaDocumentacion(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaDocumentacion);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaDocumentacion($fecha);
                                    }

                                    if (is_null($fechaEmision)|| $fechaEmision == '') {
                                        $registro->setFechaEmision(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaEmision);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaEmision($fecha);
                                    }

                                    if (is_null($fechaRespuesta)|| $fechaRespuesta == '') {
                                        $registro->setFechaRespuesta(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaRespuesta);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaRespuesta($fecha);
                                    }

                                    if (is_null($fechaAviso)|| $fechaAviso == '') {
                                        $registro->setFechaAviso(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaAviso);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaAviso($fecha);
                                    }

                                    if (is_null($fechaAplicacion)|| $fechaAplicacion == '') {
                                        $registro->setFechaAplicacion(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaAplicacion);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaAplicacion($fecha);
                                    }

                                    if (is_null($fechaEscalacion)|| $fechaEscalacion == '') {
                                        $registro->setFechaEscalacion(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaEscalacion);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaEscalacion($fecha);
                                    }

                                    if (is_null($fechaResolucion)|| $fechaResolucion == '') {
                                        $registro->setFechaResolucion(null);
                                    } else {
                                        try {
                                            $fecha = new \DateTime($fechaResolucion);
                                        } catch (\Exception $e) {
                                            $fecha = null;
                                        }
                                        $registro->setFechaResolucion($fecha);
                                    }

                                
                                    $registro->setAnoEvento($anoEvento);
                                    $registro->setAnoAsignacion($anoAsignacion);
                                    $registro->setAnoDocumentacion($anoDocumentacion);
                                    $registro->setFormaPago($formaPago);

                                    $registro->setEstatus($estatus);
                                    $registro->setTipoMaterial($tipoMaterial);
                                    $registro->setEscalado($escalado);
                                    $registro->setCaja($caja);
                                    $registro->setComentarios($comentarios);
                                    $registro->setObservaciones($observaciones);

                                    $this->manager->persist($registro);
                                    $this->manager->flush();
                                    $status = true;
                                    $errorCode = 0;
                                    $errorMsg = "RegistroGuardado";
                                }
                            }


                            $r +=1;
                        }
                    }



                    /* Para escribir un archivo
                    $writer = new Xlsx($spreadsheet);
                    $date = date("Ymdhis");
                    $name = 'datos'.$date;
                    $writer->save('build/uploads/'.$name.'.xlsx');
                    */

                    $validation['mensaje'] = "Base de datos actualizada con exito";
                    unlink('build/'.$someNewFilename);
                } catch (FileException $e) {
                    $status = false;
                    $errorCode = 1;
                    $errorMsg = "Error en Carga de archivo";
                    $validation['status'] = $status;
                    $validation['error']['code'] = $errorCode;
                    $validation['error']['mensaje'] = $errorMsg;
                }
            } else {
                $validation['currentFile'] = "notFile";
            }
        }

        return $this->returnValidation($validation);
    }



    /**
     * @Route("/api/getRegistrosBusqueda/{email}/{token}", name="getRegistrosBusqueda", methods={ "POST"})
     *
     */
    public function getRegistrosBusqueda($email, $token, Request $request, MailerInterface $mailer, RegistroRepository $repository)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA', 'ROLE_PERSONAL']);
        $limit = 25;
        $models = [];
        $maxPages = 0;
        $pagina =1;
        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }

            $form = $this->createForm(BusquedaType::class, null, [
              'csrf_protection' => false,
              ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
                  'errors' => $errors
                ], 400);
            }
            $pagina = $form->get('pagina')->getData();
            $localidad = $form->get('localidad')->getData();
            $planta = $form->get('planta')->getData();
            $tipo = $form->get('tipo')->getData();
            $descripcion = $form->get('descripcion')->getData();
            $fechaEvento = $form->get('fechaEvento')->getData();
            $fechaEvento2 = $form->get('fechaEvento2')->getData();

            $transportista = $form->get('transportista')->getData();
            $fechaEmision = $form->get('fechaEmision')->getData();
            $fechaEmision2 = $form->get('fechaEmision2')->getData();

            $fechaRespuesta = $form->get('fechaRespuesta')->getData();
            $fechaRespuesta2 = $form->get('fechaRespuesta2')->getData();

            $fechaPago = $form->get('fechaPago')->getData();
            $fechaPago2 = $form->get('fechaPago2')->getData();

            $estatus = $form->get('estatus')->getData();
            $escalado = $form->get('escalado')->getData();
            $ruta = $form->get('ruta')->getData();
            $anoEvento = $form->get('anoEvento')->getData();
            $anoDocumentacion = $form->get('anoDocumentacion')->getData();
            $anoAsignacion = $form->get('anoAsignacion')->getData();

            $busqueda = $form->get('busqueda')->getData();




            if (is_null($localidad)) {
                $localidadId='no';
            } else {
                $repositoryLocalidad = $this->getDoctrine()->getRepository(Localidad::class);
                $local = $repositoryLocalidad->findOneBy([
                'localidad' => $localidad,
              ]);
                if ($local) {
                    $localidadId = $local ->getid();
                } else {
                    $localidadId = 'no';
                }
            }

            if (is_null($planta)) {
                $plantaId='no';
            } else {
                $repositoryPlanta = $this->getDoctrine()->getRepository(Planta::class);
                $plant = $repositoryPlanta->findOneBy([
                'planta' => $planta,
              ]);
                if ($plant) {
                    $plantaId = $plant ->getid();
                } else {
                    $plantaId = 'no';
                }
            }

            if (is_null($transportista)) {
                $transportistaId='no';
            } else {
                $repositoryTransportista = $this->getDoctrine()->getRepository(Transportista::class);
                $transport = $repositoryTransportista->findOneBy([
                'transportista' => $transportista,
              ]);
                if ($transport) {
                    $transportistaId = $transport->getid();
                } else {
                    $transportistaId = 'no';
                }
            }

            if (is_null($ruta)) {
                $rutaId='no';
            } else {
                $repositoryRuta = $this->getDoctrine()->getRepository(Ruta::class);
                $rut = $repositoryRuta->findOneBy([
                'ruta' => $ruta,
              ]);
                if ($rut) {
                    $rutaId = $rut ->getid();
                } else {
                    $rutaId = 'no';
                }
            }

            if (is_null($fechaEvento)) {
                $fechaEvento = 'no';
            } else {
                try {
                    $date1 =  new \DateTime($fechaEvento);
                    $fechaEvento = $date1->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaEvento = 'no';
                }
            }
            if (is_null($fechaEvento2)) {
                $fechaEvento2 = 'no';
            } else {
                try {
                    $date1 =  new \DateTime($fechaEvento2);
                    $fechaEvento2 = $date1->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaEvento2 = 'no';
                }
            }

            if (is_null($fechaEmision)) {
                $fechaEmision = 'no';
            } else {
                try {
                    $date2 =  new \DateTime($fechaEmision);
                    $fechaEmision = $date2->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaEmision = 'no';
                }
            }
            if (is_null($fechaEmision2)) {
                $fechaEmision2 = 'no';
            } else {
                try {
                    $date2 =  new \DateTime($fechaEmision2);
                    $fechaEmision2 = $date2->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaEmision2 = 'no';
                }
            }

            if (is_null($fechaRespuesta)) {
                $fechaRespuesta = 'no';
            } else {
                try {
                    $date3 =  new \DateTime($fechaRespuesta);
                    $fechaRespuesta = $date3->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaRespuesta = 'no';
                }
            }
            if (is_null($fechaRespuesta2)) {
                $fechaRespuesta2 = 'no';
            } else {
                try {
                    $date3 =  new \DateTime($fechaRespuesta2);
                    $fechaRespuesta2 = $date3->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaRespuesta2 = 'no';
                }
            }

            if (is_null($fechaPago)) {
                $fechaPago = 'no';
            } else {
                try {
                    $date4 =  new \DateTime($fechaPago);
                    $fechaPago = $date4->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaPago = 'no';
                }
            }
            if (is_null($fechaPago2)) {
                $fechaPago2 = 'no';
            } else {
                try {
                    $date4 =  new \DateTime($fechaPago2);
                    $fechaPago2 = $date4->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaPago2 = 'no';
                }
            }


            if (is_null($tipo)) {
                $tipo = 'no';
            }
            if (is_null($descripcion)) {
                $descripcion = 'no';
            }
            if (is_null($estatus)) {
                $estatus = 'no';
            }
            if (is_null($escalado)) {
                $escalado = 'no';
            }
            if (is_null($busqueda)) {
                $busqueda = 'no';
            }
            if (is_null($anoAsignacion)) {
                $anoAsignacion = 'no';
            }
            if (is_null($anoEvento)) {
                $anoEvento = 'no';
            }
            if (is_null($anoDocumentacion)) {
                $anoDocumentacion = 'no';
            }

            $registros = $repository->getAll(
                intval($pagina),
                $limit,
                $localidadId,
                $plantaId,
                $tipo,
                $descripcion,
                $fechaEvento,
                $fechaEvento2,
                $transportistaId,
                $fechaEmision,
                $fechaEmision2,
                $fechaRespuesta,
                $fechaRespuesta2,
                $fechaPago,
                $fechaPago2,
                $estatus,
                $escalado,
                $rutaId,
                $busqueda,
                $anoEvento,
                $anoAsignacion,
                $anoDocumentacion
            );

            $registrosBusqueda = $registros['paginator'];
            $maxPages = ceil($registros['paginator']->count() / $limit);

            foreach ($registrosBusqueda as $registro) {
                $models[] = $this->getRegistroListModel($registro);
            }
            $validation['total']= $registros['paginator']->count();

            if (intval($pagina) == 1) {
                $registrosNP = $repository->getAllNP(
                    $localidadId,
                    $plantaId,
                    $tipo,
                    $descripcion,
                    $fechaEvento,
                    $fechaEvento2,
                    $transportistaId,
                    $fechaEmision,
                    $fechaEmision2,
                    $fechaRespuesta,
                    $fechaRespuesta2,
                    $fechaPago,
                    $fechaPago2,
                    $estatus,
                    $escalado,
                    $rutaId,
                    $busqueda,
                    $anoEvento,
                    $anoAsignacion,
                    $anoDocumentacion
                );


                $transportistas = [];

                $aceptado = 0;
                $reclamado = 0;
                $proceso = 0;
                $cancelado =0;
                $recuperado = 0;
                $rechazado = 0;

                foreach ($registrosNP as $regNP) {
                    $trans = $regNP ->getTransportista();
                    if ($trans) {
                        if (in_array($trans->getTransportista(), array_keys($transportistas))) {
                            $transportistas[$trans->getTransportista()] += 1;
                        } else {
                            $transportistas[$trans->getTransportista()] = 1;
                        }
                    }

                    if ($regNP->getAceptado()) {
                        $aceptado += intval($regNP->getAceptado());
                    } else {
                        $aceptado += 0;
                    }
                    if ($regNP->getReclamadoMXN()) {
                        $reclamado += intval($regNP->getReclamadoMXN());
                    } else {
                        $reclamado += 0;
                    }
                    if ($regNP->getReclamoProceso()) {
                        $proceso += intval($regNP->getReclamoProceso());
                    } else {
                        $proceso += 0;
                    }
                    if ($regNP->getCancelado()) {
                        $cancelado += intval($regNP->getCancelado());
                    } else {
                        $cancelado += 0;
                    }
                    if ($regNP->getRecuperado()) {
                        $recuperado += intval($regNP->getRecuperado());
                    } else {
                        $recuperado += 0;
                    }
                    if ($regNP->getExcedente()) {
                        $rechazado += intval($regNP->getExcedente());
                    } else {
                        $rechazado += 0;
                    }
                }
                $validation['transportistas']= $transportistas;

                $validation['aceptado']= $aceptado;
                $validation['reclamado']= $reclamado;
                $validation['proceso']= $proceso;
                $validation['cancelado']= $cancelado;
                $validation['recuperado']= $recuperado;
                $validation['rechazado']= $rechazado;
            }
        }

        $validation['items']= $models;
        $validation['maxPages']= $maxPages;
        $validation['limite']= $limit;
        $validation['pagina']= intval($pagina);




        return $this->returnValidation($validation);
    }

    public function getRegistroListModel($registro)
    {
        $model = new RegistroListModel();
        $model->id = $registro->getId();

        if ($registro->getReferencia()) {
            $model->referencia = $registro->getReferencia();
        } else {
            $model->referencia = 'Sin Referencia';
        }

        if ($registro->getLocalidad()) {
            $model->localidad = $registro->getLocalidad()->getLocalidad();
        } else {
            $model->localidad = 'Sin Localidad';
        }

        if ($registro->getTransportista()) {
            $model->transportista = $registro->getTransportista()->getTransportista();
        } else {
            $model->transportista = 'Sin Transportista';
        }

        if ($registro->getTipo()) {
            $model->tipo = $registro->getTipo();
        } else {
            $model->tipo = 'Sin Tipo de daño';
        }

        if ($registro->getFechaEvento()) {
            $model->fecha = $registro->getFechaEvento()->format($this->dateFormat);
        } else {
            $model->fecha = 'Sin Fecha';
        }

        $model->estatus = $registro->getEstatus();

        return $model;
    }

    /**
     * @Route("/api/getExcelRegistros/{email}/{token}", name="getExcelRegistros", methods={ "POST"})
     *
     */
    public function getExcelRegistros($email, $token, Request $request, MailerInterface $mailer)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA', 'ROLE_PERSONAL']);
        $limit = 25;
        $models = [];
        $maxPages = 0;
        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }

            $form = $this->createForm(BusquedaType::class, null, [
              'csrf_protection' => false,
              ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
                  'errors' => $errors
                ], 400);
            }
            $pagina = $form->get('pagina')->getData();
            $localidad = $form->get('localidad')->getData();
            $planta = $form->get('planta')->getData();
            $tipo = $form->get('tipo')->getData();
            $descripcion = $form->get('descripcion')->getData();
            $fechaEvento = $form->get('fechaEvento')->getData();
            $transportista = $form->get('transportista')->getData();
            $fechaEmision = $form->get('fechaEmision')->getData();
            $fechaRespuesta = $form->get('fechaRespuesta')->getData();
            $fechaPago = $form->get('fechaPago')->getData();
            $estatus = $form->get('estatus')->getData();
            $escalado = $form->get('escalado')->getData();
            $ruta = $form->get('ruta')->getData();
            $busqueda = $form->get('busqueda')->getData();
            $anoEvento = $form->get('anoEvento')->getData();
            $anoDocumentacion = $form->get('anoDocumentacion')->getData();
            $anoAsignacion = $form->get('anoAsignacion')->getData();

            if (is_null($localidad)) {
                $localidadId='no';
            } else {
                $repositoryLocalidad = $this->getDoctrine()->getRepository(Localidad::class);
                $local = $repositoryLocalidad->findOneBy([
                'localidad' => $localidad,
              ]);
                if ($local) {
                    $localidadId = $local ->getid();
                } else {
                    $localidadId = 'no';
                }
            }

            if (is_null($planta)) {
                $plantaId='no';
            } else {
                $repositoryPlanta = $this->getDoctrine()->getRepository(Planta::class);
                $plant = $repositoryPlanta->findOneBy([
                'planta' => $planta,
              ]);
                if ($plant) {
                    $plantaId = $plant ->getid();
                } else {
                    $plantaId = 'no';
                }
            }

            if (is_null($transportista)) {
                $transportistaId='no';
            } else {
                $repositoryTransportista = $this->getDoctrine()->getRepository(Transportista::class);
                $transport = $repositoryTransportista->findOneBy([
                'transportista' => $transportista,
              ]);
                if ($transport) {
                    $transportistaId = $transport ->getid();
                } else {
                    $transportistaId = 'no';
                }
            }

            if (is_null($ruta)) {
                $rutaId='no';
            } else {
                $repositoryRuta = $this->getDoctrine()->getRepository(Ruta::class);
                $rut = $repositoryRuta->findOneBy([
                'ruta' => $ruta,
              ]);
                if ($rut) {
                    $rutaId = $rut ->getid();
                } else {
                    $rutaId = 'no';
                }
            }

            if (is_null($fechaEvento)) {
                $fechaEvento = 'no';
            } else {
                try {
                    $date1 =  new \DateTime($fechaEvento);
                    $fechaEvento = $date1->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaEvento = 'no';
                }
            }

            if (is_null($fechaEmision)) {
                $fechaEmision = 'no';
            } else {
                try {
                    $date2 =  new \DateTime($fechaEmision);
                    $fechaEmision = $date2->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaEmision = 'no';
                }
            }

            if (is_null($fechaRespuesta)) {
                $fechaRespuesta = 'no';
            } else {
                try {
                    $date3 =  new \DateTime($fechaRespuesta);
                    $fechaRespuesta = $date3->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaRespuesta = 'no';
                }
            }

            if (is_null($fechaPago)) {
                $fechaPago = 'no';
            } else {
                try {
                    $date4 =  new \DateTime($fechaPago);
                    $fechaPago = $date4->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaPago = 'no';
                }
            }


            if (is_null($tipo)) {
                $tipo = 'no';
            }
            if (is_null($descripcion)) {
                $descripcion = 'no';
            }
            if (is_null($estatus)) {
                $estatus = 'no';
            }
            if (is_null($escalado)) {
                $escalado = 'no';
            }
            if (is_null($busqueda)) {
                $busqueda = 'no';
            }
            if (is_null($anoAsignacion)) {
                $anoAsignacion = 'no';
            }
            if (is_null($anoEvento)) {
                $anoEvento = 'no';
            }
            if (is_null($anoDocumentacion)) {
                $anoDocumentacion = 'no';
            }



            $repository = $this->getDoctrine()->getRepository(Registro::class);


            $registrosNP = $repository->getAllNP(
                $localidadId,
                $plantaId,
                $tipo,
                $descripcion,
                $fechaEvento,
                $transportistaId,
                $fechaEmision,
                $fechaRespuesta,
                $fechaPago,
                $estatus,
                $escalado,
                $rutaId,
                $busqueda,
                $anoEvento,
                $anoAsignacion,
                $anoDocumentacion
            );


            //$writer = WriterEntityFactory::createXLSXWriter();
            // $writer = WriterEntityFactory::createODSWriter();
            $writer = WriterEntityFactory::createCSVWriter();
            $date = date("Ymdhis");
            $name = 'registrosDescargados'.$date.'.csv';

            $writer->openToFile('build/'.$name); // write data to a file or to a PHP stream
            //$writer->openToBrowser($fileName); // stream data directly to the browser
            $values = [
              'LOCALIDAD',
              'PLANTA',
              'TIPO DE EVENTO',
              'DESCRIPCIÓN DE EVENTO',
              'TRANSPORTISTA',
              'REFERENCIA',
              'MONTO RECLAMADO USD',
              'MONTO RECLAMADO MXN2',
              'MONTO ACEPTADO',
              'MONTO RECUPERADO',
              'AJUSTES EN MGO',
              'RECLAMO EN DOCUMENTACIÓN',
              'RECLAMO EN PROCESO	',
              'AJUSTE/ REVERSIÓN DE PARTIDAS',
              'CANCELADO',
              'FLETE DEL BI30 NO INCLUIDO EN RECLAMO',
              'MENORES DE USD$500',
              'EXCEDENTE DE CONTRATO',
              'MONTO ESTIMADO A RECUPERAR',
              'FECHA DEL EVENTO',
              'FECHA ASIGNACIÓN O NOTIFICACIÓN',
              'DIAS DE EVENTO A NOTIFICACION',
              'FECHA DE DOCUMENTACIÓN',
              'DIAS DE NOTIFICACION A DOCUMENTACION',
              'FECHA DE EMISIÓN RV',
              'DIAS DE DOCUMENTACION A EMISION',
              'FECHA RESPUESTA CARRIER',
              'DÍAS DE EMISIÓN A RESPUESTA',
              'FECHA AVISO DE PAGO O SOLICITUD DE DÉBITO',
              'FECHA DE APLICACIÓN CONTABLE',
              'TOTAL DIAS DE ATRASO DESDE LA EMISION',
              'ESTATUS',
              'COMENTARIOS',
              'FECHA DE ÚLTIMA ACTUALIZACIÓN',
              'OBSERVACIONES POR DIFERENCIAS Y CANCELACIONES',
              'TIPO DE MATERIAL',
              'ESCALADO',
              'ÁREA',
              'FECHA ESCALACION',
              'FECHA DE RESOLUCIÓN',
              'PROVEEDOR',
              'RUTA',
              'CAJA',
              'AÑO DE EVENTO',
              'AÑO DE ASIGNACION',
              'AÑO DE DOCUMENTACION',
              'FORMA DE PAGO',
            ];

            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
            $writer->addRow($rowFromValues);

            foreach ($registrosNP as $regNP) {
                $model = $this->getRegistroDescargaModel($regNP);
                $rowFromValues = WriterEntityFactory::createRowFromArray($model);
                $writer->addRow($rowFromValues);
            }
            $writer->close();
            $validation['enlace'] = $name;
        }
        return $this->returnValidation($validation);
    }

    public function getRegistroFullModel($registro)
    {
        $model = new RegistroFullModel();

        $model->id = $registro->getId();

        if ($registro->getLocalidad()) {
            $model->localidad = $registro->getLocalidad()->getLocalidad();
        } else {
            $model->localidad = '';
        }

        if ($registro->getPlanta()) {
            $model->planta = $registro->getPlanta()->getPlanta();
        } else {
            $model->planta = '';
        }

        $model->tipo = $registro->getTipo();

        $model->descripcion = $registro->getDescripcion();

        if ($registro->getTransportista()) {
            $model->transportista = $registro->getTransportista()->getTransportista();
        } else {
            $model->transportista = '';
        }

        $model->referencia = $registro -> getReferencia();
        $model->reclamadoUSD = $registro -> getReclamadoUSD();
        $model->reclamadoMXN= $registro -> getReclamadoMXN();
        $model->aceptado= $registro -> getAceptado();
        $model->recuperado =$registro -> getRecuperado();
        $model->ajustes= $registro -> getAjustes();
        $model->reclamoDocumentacion= $registro -> getReclamoDocumentacion();
        $model->reclamoProceso =$registro -> getReclamoProceso();
        $model->ajuste =$registro -> getAjuste();
        $model->cancelado =$registro -> getCancelado();
        $model->flete= $registro -> getFlete();
        $model->menores= $registro -> getMenores();
        $model->excedente =$registro -> getExcedente();
        $model->estimado= $registro -> getEstimado();

        if ($registro->getFechaEvento()) {
            $model->fechaEvento = $registro->getFechaEvento()->format($this->dateFormatRegistro);
        } else {
            $model->fechaEvento = '';
        }
        if ($registro->getFechaAsignacion()) {
            $model->fechaAsignacion = $registro->getFechaAsignacion()->format($this->dateFormatRegistro);
        } else {
            $model->fechaAsignacion = '';
        }

        if ($registro->getFechaEvento() && $registro->getFechaAsignacion()) {
            $interval = date_diff($registro->getFechaEvento(), $registro->getFechaAsignacion());
            $model->eventoNotificacion = $interval->format('%a');
        } else {
            $model->eventoNotificacion = '';
        }

        if ($registro->getFechaDocumentacion()) {
            $model->fechaDocumentacion = $registro->getFechaDocumentacion()->format($this->dateFormatRegistro);
        } else {
            $model->fechaDocumentacion = '';
        }

        if ($registro->getFechaDocumentacion() && $registro->getFechaAsignacion()) {
            $interval = date_diff($registro->getFechaAsignacion(), $registro->getFechaDocumentacion());
            $model->notificacionDocumentacion = $interval->format('%a');
        } else {
            $model->notificacionDocumentacion = '';
        }

        if ($registro->getFechaEmision()) {
            $model->fechaEmision = $registro->getFechaEmision()->format($this->dateFormatRegistro);
        } else {
            $model->fechaEmision = '';
        }

        if ($registro->getFechaDocumentacion() && $registro->getFechaEmision()) {
            $interval = date_diff($registro->getFechaDocumentacion(), $registro->getFechaEmision());
            $model->documentacionEmision = $interval->format('%a');
        } else {
            $model->documentacionEmision = '';
        }

        if ($registro->getFechaRespuesta()) {
            $model->fechaRespuesta = $registro->getFechaRespuesta()->format($this->dateFormatRegistro);
        } else {
            $model->fechaRespuesta = '';
        }

        if ($registro->getFechaRespuesta() && $registro->getFechaEmision()) {
            $interval = date_diff($registro->getFechaEmision(), $registro->getFechaRespuesta());
            $model->emisionRespuesta = $interval->format('%a');
        } else {
            $model->emisionRespuesta = '';
        }

        if ($registro->getFechaAviso()) {
            $model->fechaAviso = $registro->getFechaAviso()->format($this->dateFormatRegistro);
        } else {
            $model->fechaAviso = '';
        }

        if ($registro->getFechaAplicacion()) {
            $model->fechaAplicacion = $registro->getFechaAplicacion()->format($this->dateFormatRegistro);
        } else {
            $model->fechaAplicacion = '';
        }

        if ($registro->getFechaEmision()) {
            $interval = date_diff($registro->getFechaEmision(), new \DateTime());
            $model->diasTotales = $interval->format('%a');
        } else {
            $model->diasTotales = '';
        }

        $model->estatus = $registro ->getEstatus();
        $model->comentarios= $registro ->getComentarios();

        if ($registro->getActualizacion()) {
            $model->fechaActualizacion = $registro->getActualizacion()->format($this->dateFormatRegistro);
        } else {
            $model->fechaActualizacion = '';
        }

        $model->observaciones= $registro ->getObservaciones();
        $model->tipoMaterial= $registro ->getTipoMaterial();
        $model->escalado= $registro ->getEscalado();

        if ($registro->getArea()) {
            $model->area = $registro->getArea()->getArea();
        } else {
            $model->area = '';
        }


        if ($registro->getFechaEscalacion()) {
            $model->fechaEscalacion = $registro->getFechaEscalacion()->format($this->dateFormatRegistro);
        } else {
            $model->fechaEscalacion = '';
        }
        if ($registro->getFechaResolucion()) {
            $model->fechaResolucion = $registro->getFechaResolucion()->format($this->dateFormatRegistro);
        } else {
            $model->fechaResolucion = '';
        }

        if ($registro->getProveedor()) {
            $model->proveedor = $registro->getProveedor()->getProveedor();
        } else {
            $model->proveedor = '';
        }
        if ($registro->getRuta()) {
            $model->ruta = $registro->getRuta()->getRuta();
        } else {
            $model->ruta = '';
        }

        $model->caja= $registro ->getCaja();

        $model->anoEvento= $registro ->getAnoEvento();
        $model->anoAsignacion= $registro ->getAnoAsignacion();
        $model->anoDocumentacion= $registro ->getAnoDocumentacion();
        $model->formaPago= $registro ->getFormaPago();


        return $model;
    }

    public function getRegistroDescargaModel($regNP)
    {
        $model = $this->getRegistroFullModel($regNP);

        return [
          $model->localidad,
          $model->planta,

          $model->tipo,
          $model->descripcion,
          $model->transportista,

          $model->referencia,
          $model->reclamadoUSD,
          $model->reclamadoMXN,
          $model->aceptado,
          $model->recuperado,
          $model->ajustes,
          $model->reclamoDocumentacion,
          $model->reclamoProceso,
          $model->ajuste,
          $model->cancelado,
          $model->flete,
          $model->menores,
          $model->excedente,
          $model->estimado,
          $model->fechaEvento,
          $model->fechaAsignacion,
          $model->eventoNotificacion,
          $model->fechaDocumentacion,
          $model->notificacionDocumentacion,
          $model->fechaEmision,
          $model->documentacionEmision,
          $model->fechaRespuesta,
          $model->emisionRespuesta,
          $model->fechaAviso,
          $model->fechaAplicacion,
          $model->diasTotales,
          $model->estatus,
          $model->comentarios,
          $model->fechaActualizacion,
          $model->observaciones,
          $model->tipoMaterial,
          $model->escalado,
          $model->area,
          $model->fechaEscalacion,
          $model->fechaResolucion,
          $model->proveedor,
          $model->ruta,
          $model->caja,
          $model->anoEvento,
          $model->anoAsignacion,
          $model->anoDocumentacion,
          $model->formaPago
        ];
    }

    /**
     * @Route("/api/getRegistro/{email}/{token}/{registro_id}", name="getRegistro", methods={"GET"})
     *
     */
    public function getRegistro($email, $token, $registro_id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        $modelo = '';
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Registro::class);

            $registro = $repository->findOneBy([
              'id'=> $registro_id
            ]);

            if ($registro) {
                $modelo = $this->getRegistroFullModel($registro);
                $status = true;
                $errorCode = 0;
                $errorMsg = "Sin Error";
                $validation['item'] = $modelo;
            } else {
                $status = false;
                $errorCode = 1;
                $errorMsg = "No se encontro el registro";
            }
        } else {
            $status = false;
            $errorCode = 2;
            $errorMsg = "Fallo la validacion";
        }
        $validation['status'] = $status;
        $validation['error']['code'] = $errorCode;
        $validation['error']['mensaje'] = $errorMsg;
        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/getExcelRegistro/{email}/{token}/{registro_id}", name="getExcelRegistro", methods={ "GET"})
     *
     */
    public function getExcelRegistro($email, $token, $registro_id)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA', 'ROLE_PERSONAL']);
        $limit = 25;
        $models = [];
        $maxPages = 0;
        if ($validation['status']) {

            //$writer = WriterEntityFactory::createXLSXWriter();
            // $writer = WriterEntityFactory::createODSWriter();
            $writer = WriterEntityFactory::createCSVWriter();
            $date = date("Ymdhis");
            $name = 'registrosDescargados'.$date.'.csv';

            $writer->openToFile('build/'.$name); // write data to a file or to a PHP stream
            //$writer->openToBrowser($fileName); // stream data directly to the browser
            $values = [
              'LOCALIDAD',
              'PLANTA',
              'TIPO DE EVENTO',
              'DESCRIPCIÓN DE EVENTO',
              'TRANSPORTISTA',
              'REFERENCIA',
              'MONTO RECLAMADO USD',
              'MONTO RECLAMADO MXN2',
              'MONTO ACEPTADO',
              'MONTO RECUPERADO',
              'AJUSTES EN MGO',
              'RECLAMO EN DOCUMENTACIÓN',
              'RECLAMO EN PROCESO	',
              'AJUSTE/ REVERSIÓN DE PARTIDAS',
              'CANCELADO',
              'FLETE DEL BI30 NO INCLUIDO EN RECLAMO',
              ' MENORES DE USD$500',
              'EXCEDENTE DE CONTRATO',
              'MONTO ESTIMADO A RECUPERAR',
              'FECHA DEL EVENTO',
              'FECHA ASIGNACIÓN O NOTIFICACIÓN',
              'DIAS DE EVENTO A NOTIFICACION',
              'FECHA DE DOCUMENTACIÓN',
              'DIAS DE NOTIFICACION A DOCUMENTACION',
              'FECHA DE EMISIÓN RV',
              'DIAS DE DOCUMENTACION A EMISION',
              'FECHA RESPUESTA CARRIER',
              'DÍAS DE EMISIÓN A RESPUESTA',
              'FECHA AVISO DE PAGO O SOLICITUD DE DÉBITO',
              'FECHA DE APLICACIÓN CONTABLE',
              'TOTAL DIAS DE ATRASO DESDE LA EMISION',
              'ESTATUS',
              'COMENTARIOS',
              'FECHA DE ÚLTIMA ACTUALIZACIÓN',
              'OBSERVACIONES POR DIFERENCIAS Y CANCELACIONES',
              'TIPO DE MATERIAL',
              'ESCALADO',
              'ÁREA',
              'FECHA ESCALACION',
              'FECHA DE RESOLUCIÓN',
              'PROVEEDOR',
              'RUTA',
              'CAJA',
              'AÑO DE EVENTO',
              'AÑO DE ASIGNACION',
              'AÑO DE DOCUMENTACION',
              'FORMA DE PAGO',
            ];

            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
            $writer->addRow($rowFromValues);

            $repository = $this->getDoctrine()->getRepository(Registro::class);

            $registro = $repository->findOneBy([
              'id'=> $registro_id
            ]);

            $model = $this->getRegistroDescargaModel($registro);
            $rowFromValues = WriterEntityFactory::createRowFromArray($model);
            $writer->addRow($rowFromValues);
            $writer->close();
            $validation['enlace'] = $name;
        }
        return $this->returnValidation($validation);
    }

    /** 
     * @Route("/api/editRegistro/{email}/{token}", name="editRegistro", methods={"POST"})
     *
     */
    public function editRegistro($email, $token, Request $request, RegistroRepository $registroRepository)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_CAPTURISTA']);

        if ($validation['status']) {
            $data = json_decode($request->getContent(), true);
            if ($data === null) {
                throw new BadRequestHttpException('Invalid JSON');
            }

            $form = $this->createForm(RegistroEditType::class, null, [
          'csrf_protection' => false,
        ]);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);

                return $this->createApiResponse([
              'errors' => $errors
          ], 400);
            }
            $id =   $form->get('id')->getData();

            $localidad =   $form->get('localidad')->getData();
            $planta =   $form->get('planta')->getData();
            $tipo =   $form->get('tipo')->getData();
            $descripcion =   $form->get('descripcion')->getData();
            $transportista =   $form->get('transportista')->getData();
            $referencia =   $form->get('referencia')->getData();
            $reclamadoUSD =   $form->get('reclamadoUSD')->getData();
            $reclamadoMXN =   $form->get('reclamadoMXN')->getData();
            $aceptado =   $form->get('aceptado')->getData();
            $recuperado =   $form->get('recuperado')->getData();

            $ajustes =   $form->get('ajustes')->getData();
            $reclamoDocumentacion =   $form->get('reclamoDocumentacion')->getData();
            $reclamoProceso =   $form->get('reclamoProceso')->getData();
            $ajuste =   $form->get('ajuste')->getData();
            $cancelado =   $form->get('cancelado')->getData();
            $flete =   $form->get('flete')->getData();
            $menores =   $form->get('menores')->getData();
            $excedente =   $form->get('excedente')->getData();
            $estimado =   $form->get('estimado')->getData();
            $fechaEvento =   $form->get('fechaEvento')->getData();
            $fechaAsignacion =   $form->get('fechaAsignacion')->getData();
            $fechaDocumentacion =   $form->get('fechaDocumentacion')->getData();
            $fechaEmision =   $form->get('fechaEmision')->getData();
            $fechaRespuesta =   $form->get('fechaRespuesta')->getData();
            $fechaAviso =   $form->get('fechaAviso')->getData();
            $fechaAplicacion =   $form->get('fechaAplicacion')->getData();

            $estatus =   $form->get('estatus')->getData();
            $tipoMaterial =   $form->get('tipoMaterial')->getData();
            $escalado =   $form->get('escalado')->getData();
            $area =   $form->get('area')->getData();
            $proveedor =   $form->get('proveedor')->getData();

            $fechaEscalacion =   $form->get('fechaEscalacion')->getData();
            $fechaResolucion =   $form->get('fechaResolucion')->getData();
            $ruta =   $form->get('ruta')->getData();
            $caja =   $form->get('caja')->getData();
            $comentarios =   $form->get('comentarios')->getData();
            $observaciones =   $form->get('observaciones')->getData();
            $anoEvento =   $form->get('anoEvento')->getData();
            $anoAsignacion =   $form->get('anoAsignacion')->getData();
            $anoDocumentacion =   $form->get('anoDocumentacion')->getData();
            $formaPago =   $form->get('formaPago')->getData();

            $status =false;
            $validation = [
            'status' => $status
        ];
            $registro = $registroRepository->find($id);

            if (is_null($registro)) {
                $status = false;
                $errorCode = 4;
                $errorMsg = "Registro no editado";
            } else {
                $createdDate = new \DateTime();
                $registro->setActualizacion($createdDate);

                if ($localidad) {
                    $localidadRepository = $this->getDoctrine()->getRepository(Localidad::class);
                    $local = $localidadRepository->findOneBy([
                      'localidad' => $localidad
                    ]);
                    if ($local) {
                        $registro->setLocalidad($local);
                    } else {
                        $newLocalidad = new Localidad();
                        $newLocalidad ->setLocalidad($localidad);
                        $this->manager->persist($newLocalidad);
                        $registro->setLocalidad($newLocalidad);
                    }
                } else {
                    $registro->setLocalidad(null);
                }


                if ($transportista) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Transportista::class);
                    $trans = $transportistaRepository->findOneBy([
                    'transportista' => $transportista
                  ]);
                    if ($trans) {
                        $registro->setTransportista($trans);
                    } else {
                        $newTranspor = new Transportista();
                        $newTranspor ->setTransportista($transportista);
                        $this->manager->persist($newTranspor);
                        $registro->setTransportista($newTranspor);
                    }
                } else {
                    $registro->setTransportista(null);
                }

                if ($planta) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Planta::class);
                    $plant = $transportistaRepository->findOneBy([
                    'planta' => $planta
                  ]);
                    if ($plant) {
                        $registro->setPlanta($plant);
                    } else {
                        $newPlant = new Planta();
                        $newPlant ->setPlanta($planta);
                        $newPlant ->setLocalidad($registro->getLocalidad());
                        $this->manager->persist($newPlant);
                        $registro->setPlanta($newPlant);
                    }
                } else {
                    $registro->setPlanta(null);
                }

                if ($area) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Area::class);
                    $ar = $transportistaRepository->findOneBy([
                    'area' => $area
                  ]);
                    if ($ar) {
                        $registro->setArea($ar);
                    } else {
                        $newArea = new Area();
                        $newArea ->setArea($area);
                        $this->manager->persist($newArea);

                        $registro->setArea($newArea);
                    }
                } else {
                    $registro->setArea(null);
                }

                if ($ruta) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Ruta::class);
                    $ar = $transportistaRepository->findOneBy([
                    'ruta' => $ruta
                  ]);
                    if ($ar) {
                        $registro->setRuta($ar);
                    } else {
                        $newRuta = new Ruta();
                        $newRuta ->setRuta($ruta);
                        $this->manager->persist($newRuta);
                        $registro->setRuta($newRuta);
                    }
                } else {
                    $registro->setRuta(null);
                }

                if ($proveedor) {
                    $transportistaRepository = $this->getDoctrine()->getRepository(Proveedor::class);
                    $ar = $transportistaRepository->findOneBy([
                    'proveedor' => $proveedor
                  ]);
                    if ($ar) {
                        $registro->setProveedor($ar);
                    } else {
                        $newProv = new Proveedor();
                        $newProv ->setProveedor($proveedor);
                        $this->manager->persist($newProv);

                        $registro->setProveedor($newProv);
                    }
                } else {
                    $registro->setProveedor(null);
                }

                $registro->setRecuperado($recuperado);

                $registro->setTipo($tipo);
                $registro->setDescripcion($descripcion);
                $registro->setReferencia($referencia);
                $registro->setReclamadoUSD($reclamadoUSD);
                $registro->setReclamadoMXN($reclamadoMXN);
                $registro->setAceptado($aceptado);
                $registro->setAjustes($ajustes);
                $registro->setReclamoDocumentacion($reclamoDocumentacion);
                $registro->setReclamoProceso($reclamoProceso);
                $registro->setAjuste($ajuste);
                $registro->setCancelado($cancelado);

                $registro->setFlete($flete);
                $registro->setMenores($menores);
                $registro->setExcedente($excedente);
                $registro->setEstimado($estimado);

                if (is_null($fechaEvento) || $fechaEvento == '') {
                    $registro->setFechaEvento(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaEvento);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaEvento($fecha);
                }

                if (is_null($fechaAsignacion) || $fechaAsignacion == '') {
                    $registro->setFechaAsignacion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaAsignacion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaAsignacion($fecha);
                }

                if (is_null($fechaDocumentacion)|| $fechaDocumentacion == '') {
                    $registro->setFechaDocumentacion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaDocumentacion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaDocumentacion($fecha);
                }

                if (is_null($fechaEmision)|| $fechaEmision == '') {
                    $registro->setFechaEmision(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaEmision);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaEmision($fecha);
                }

                if (is_null($fechaRespuesta)|| $fechaRespuesta == '') {
                    $registro->setFechaRespuesta(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaRespuesta);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaRespuesta($fecha);
                }

                if (is_null($fechaAviso)|| $fechaAviso == '') {
                    $registro->setFechaAviso(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaAviso);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaAviso($fecha);
                }

                if (is_null($fechaAplicacion)|| $fechaAplicacion == '') {
                    $registro->setFechaAplicacion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaAplicacion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaAplicacion($fecha);
                }

                if (is_null($fechaEscalacion)|| $fechaEscalacion == '') {
                    $registro->setFechaEscalacion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaEscalacion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaEscalacion($fecha);
                }

                if (is_null($fechaResolucion)|| $fechaResolucion == '') {
                    $registro->setFechaResolucion(null);
                } else {
                    try {
                        $fecha = new \DateTime($fechaResolucion);
                    } catch (\Exception $e) {
                        $fecha = null;
                    }
                    $registro->setFechaResolucion($fecha);
                }

                $registro->setAnoEvento($anoEvento);
                $registro->setAnoAsignacion($anoAsignacion);                
                $registro->setAnoDocumentacion($anoDocumentacion);
                $registro->setformaPago($formaPago);

                $registro->setEstatus($estatus);
                $registro->setTipoMaterial($tipoMaterial);
                $registro->setEscalado($escalado);
                $registro->setCaja($caja);
                $registro->setComentarios($comentarios);
                $registro->setObservaciones($observaciones);

                $this->manager->persist($registro);
                $this->manager->flush();
                $status = true;
                $errorCode = 0;
                $errorMsg = "RegistroGuardado";

                $registro = $registroRepository->find($id);
                $validation['item'] = $this->getRegistroFullModel($registro);
                ;
            }
            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }

    /**
     * @Route("/api/documentarReporte/{email}/{token}/{reporteId}", name="documentarReporte", methods={ "GET"})
     *
     */
    public function documentarReporte($email, $token, $reporteId)
    {
        $validation = $this->checkToken($email, $token, ['ROLE_ADMINISTRADOR', 'ROLE_PERSONAL', 'ROLE_CAPTURISTA']);
        if ($validation['status']) {
            $repository = $this->getDoctrine()->getRepository(Reporte::class);
            $reporte = $repository->findOneBy([
            'id' => $reporteId]);
            $reporte ->setDocumentado(true);

            $this->manager->persist($reporte);
            $this->manager->flush();

            $status = true;
            $errorCode = 0;
            $errorMsg = "Sin Error";

            $validation['status'] = $status;
            $validation['error']['code'] = $errorCode;
            $validation['error']['mensaje'] = $errorMsg;
        }

        return $this->returnValidation($validation);
    }
}