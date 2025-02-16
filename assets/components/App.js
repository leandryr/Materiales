import React , { useState, useEffect }   from "react";
import { Route, Switch } from "react-router-dom";
import Login from "../pages/Login/Login";
import Home from "../pages/Home/Home";
import { withRouter } from 'react-router-dom';


import {login, getTransportistas, getLocalidades,
  getRutas, getPlantas, getAreas, getTipos, getDescripciones,
  getProveedores, getExcelRegistros, deleteFile,
  getRegistrosBusqueda,
getExcelRegistro
} from '../api/api.js';


function App(props) {
  const {history} = props;
  const [credentials, handleCredentials] = useState({token: null, username:null, name:null, rol:null});
  const [errorMessage, handleErrorMessage] = useState('');
  const [reporte ,setReporte] = useState(null);
  const [documentado ,setDocumentado] = useState(null);

  const [onRep, setOnRep] = useState(false);
  const [loged, setLoged] = useState(false);
  const [registro, setRegistro] = useState(false);
  const [onReg, setOnReg] = useState(false);
  const [onDoc, setOnDoc] = useState(false);

  const [transportistas, setTransportistas] = useState(null);
  const [localidades, setLocalidades] = useState(null);
  const [rutas, setRutas] = useState(null);
  const [plantas, setPlantas] = useState(null);
  const [areas, setAreas] = useState(null);
  const [proveedores, setProveedores] = useState(null);
  const [tipos, setTipos] = useState(null);

  const [descripciones, setDescripciones] = useState(null);

  const [registros, setRegistros] = useState(null);
  const [transportistasGrafica, setTransportistasGrafica] = useState(null);

  const [maxPages, setMaxPages] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [limite, setLimite] = useState(25);
  const [filtros, setFiltros] = useState(
  {
    localidad: '',
    planta: '',
    tipo: '',
    descripcion: '',
    fechaEvento :'',
    fechaEvento2 :'',

    transportista: '',
    fechaEmision: '',
    fechaEmision2: '',

    fechaRespuesta: '',
    fechaRespuesta2: '',

    fechaPago: '',
    fechaPago2: '',

    estatus: '',
    escalado: '',
    ruta: '',
    anoEvento: '',
    anoAsignacion: '',
    anoDocumentacion: '',
    busqueda: '',

    pagina: 1

  });

  const [aceptado, setAceptado] = useState(0);
  const [reclamado, setReclamado] = useState(0);
  const [proceso, setProceso] = useState(0);
  const [cancelado, setCancelado] = useState(0);
  const [recuperado, setRecuperado] = useState(0);
  const [rechazado, setRechazado] = useState(0);


  useEffect(() => {
    if(loged){
      history.push("/home/historial");
    }else{
      history.push("/");
    }
  },[loged]);

  useEffect(() => {
    if(loged){
      getRegistrosBusqueda(credentials, filtros)
      .then((datos) =>{
        setRegistros(datos.validation.items);
        setMaxPages(datos.validation.maxPages);
        setLimite(datos.validation.limite);
        setCurrentPage(datos.validation.pagina);

        if (filtros.pagina === 1) {
          setTotal(datos.validation.total);
          setTransportistasGrafica(datos.validation.transportistas);
          setAceptado(datos.validation.aceptado);
          setReclamado(datos.validation.reclamado);
          setProceso(datos.validation.proceso);
          setCancelado(datos.validation.cancelado);
          setRecuperado(datos.validation.recuperado);
          setRechazado(datos.validation.rechazado);
        }

      })
      .catch((error) => {
        console.log(error);
        });
    }else{
      //Do nothing

    }
  },[filtros]);

  const handleLogin = (cred) => {
    let creden = {

    }
    login(cred)
    .then( data => {
      handleErrorMessage('');
      const token = data.token;
      const name = data.name;
      const username = data.username;
      const rol = data.roles[0];
      creden = {
        token: token,
        username: username,
        name: name,
        rol: rol
      }




      getLocalidades(creden)
      .then((datos) =>{
        setLocalidades(datos.items)
      })
      .catch((error) => {
        console.log(error);
        });

      getTransportistas(creden)
      .then((datos) =>{
        setTransportistas(datos.items)
      })
      .catch((error) => {
        console.log(error);
        });

        getRutas(creden)
        .then((datos) =>{
          setRutas(datos.items)
        })
        .catch((error) => {
          console.log(error);
          });

        getPlantas(creden)
        .then((datos) =>{
          setPlantas(datos.items)
        })
        .catch((error) => {
          console.log(error);
          });

        getAreas(creden)
        .then((datos) =>{
          setAreas(datos.items)
        })
        .catch((error) => {
          console.log(error);
          });

        getProveedores(creden)
        .then((datos) =>{
          setProveedores(datos.items)
        })
        .catch((error) => {
          console.log(error);
          });

          getTipos(creden)
          .then((datos) =>{
            setTipos(datos.items)
          })
          .catch((error) => {
            console.log(error);
            });

            getDescripciones(creden)
            .then((datos) =>{
              setDescripciones(datos.items)
            })
            .catch((error) => {
              console.log(error);
              });




    })
    .then( () => {
      handleCredentials(creden);
    })
    .then(() => {

      setLoged(true);
      setFiltros({
        localidad: '',
        planta: '',
        tipo: '',
        descripcion: '',
        fechaEvento :'',
        fechaEvento2 :'',
        transportista: '',
        fechaEmision: '',
        fechaEmision2: '',
        fechaRespuesta: '',
        fechaRespuesta2: '',
        fechaPago: '',
        fechaPago2: '',
        estatus: '',
        escalado: '',
        ruta: '',
        anoEvento: '',
        anoAsignacion: '',
        anoDocumentacion: '',

        busqueda: '',
        pagina: 1

      });
    })
    .catch( error => {
      console.log(error);
      error.response.json().then(errorsData => {
        handleErrorMessage(errorsData.error);
      });
      });




  }

  const handleLogOut = () => {
    handleCredentials({
      token: '',
      username: '',
      name: '',
      rol: ''
    });
    handleErrorMessage('Sesion Terminada')
    setLoged(false);
  }

  const handleChangeReporte = (report) => {
      setReporte(report);
      setOnRep(true);
  }
  const handleClickReporte = (report) => {
      setReporte(report);
      setOnRep(true);
  }
  const handleClickReporteDocumentado = (report) => {
      setDocumentado(report);
      setOnDoc(true);
  }

  const handleRepToFalse = () => {
    setOnRep(false);
  }
  const handleDocToFalse = () => {
    setOnDoc(false);
  }

  const handleBusqueda = (datos) => {
    setFiltros(datos);
  }

  const hanldeUpdateRegistros = (item) => {
    setRegistros(registros.map(reg => {
      if (reg.id === item.id) {
        // Create a *new* object with changes
        return { ...reg, referencia: item.referencia , localidad: item.localidad, transportista: item.transportista , tipo: item.tipo, fecha: item.fechaEvento, estatus: item.estatus};
      } else {
        // No changes
        return reg;
      }
    }));
  }

  const handleRegToFalse = () => {
    setOnReg(false);
  }

  const handleDescargaExcelRegistros = () => {
    let enlace = '';
    if(loged){
      getExcelRegistros(credentials, filtros)
      .then((datos) =>{
        openFile(datos.validation.enlace);
        enlace = datos.validation.enlace;
      })
      .then((datos) => {
        deleteFile(credentials, enlace);
      })
      .catch((error) => {
        console.log(error);
        });
    }else{
      //Do nothing

    }
  }

  const handleDescargaExcelRegistro = () => {
    let enlace = '-'
    if(loged){
      getExcelRegistro(credentials, registro.id)
      .then((datos) =>{
        openFile(datos.enlace);
        enlace = datos.enlace;
      })
      .then(() => {
        deleteFile(credentials, enlace);
      })
      .catch((error) => {
        console.log(error);
        });
    }else{
      //Do nothing

    }
  }


  const openFile = (enlace) => {
    let host = window.location.host;
    const newWindow = window.open('https://' + host + '/build/'+enlace , '_blank', 'noopener,noreferrer')
    if (newWindow){
      newWindow.opener = null
    }
  }

  const handleClickRegistro = (regis) => {
    //cambiar el registro
      setRegistro(regis);
      setOnReg(true);
  }

  return (
    <div className="container-fluid">
      <Switch>
        <Route exact path="/"
          component={() => {
            return(
             <Login
            onMessage = {errorMessage}
            onLogin={handleLogin}

            />
          );
        }} />
        <Route path="/home" component={ () => {
          return (
          < Home
          localidades = {localidades}
          transportistas = {transportistas}
          areas = {areas}
          proveedores = {proveedores}
          rutas = {rutas}
          plantas = {plantas}
          tipos = {tipos}
          descripciones = {descripciones}

          reporte = {reporte}
          maxPages = {maxPages}
          registros = {registros}
          currentPage = {currentPage}
          total = {total}
          filtros = {filtros}
          limite = {limite}
          aceptadoGrafica = {aceptado}
          reclamado = {reclamado}
          proceso = {proceso}
          canceladoGrafica = {cancelado}
          recuperado = {recuperado}
          rechazadoGrafica = {rechazado}
          transportistasGrafica = {transportistasGrafica}
          onRep = {onRep}
          onReg = {onReg}
          onDoc = {onDoc}
            onLogOut = {handleLogOut}
            credentials = {credentials}
            onChangeReporte = {handleChangeReporte}
            onClickReporte = {handleClickReporte}
            onClickDocumentado = {handleClickReporteDocumentado}

            repToFalse = {handleRepToFalse}
            regToFalse = {handleRegToFalse}
            docToFalse = {handleDocToFalse}
            documentado = {documentado}



            registro = {registro}
            onBusqueda = {handleBusqueda}
            updateRegistros = {hanldeUpdateRegistros}
            onDescargarExcerRegistros = {handleDescargaExcelRegistros}
            onClickRegistro = {handleClickRegistro}
            onDescargarExcerRegistro = {handleDescargaExcelRegistro}
          />
        );
        }}
        />
      </Switch>
    </div>
  );
}

export default withRouter(App);
