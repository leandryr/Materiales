import React, {useState, useEffect, useReducer} from 'react';
import './Home.scss';
import LogoHome from '../../components/LogoHome/LogoHome';
import Sidebar from '../../components/Sidebar/Sidebar';
import {
    BrowserRouter as Router,
    Switch,
    Route
} from "react-router-dom";

import Historial from '../Historial/Historial';
import Usuarios from '../Usuarios/Usuarios';

import NuevoRegistro from '../NuevoRegistro/NuevoRegistro';
import NuevoReporte from '../Reportes/Reportes';
import Reclamo from '../Reclamo/Reclamo';
import Reporte from '../Reporte/Reporte';
import ReporteDocumentado from '../ReporteDocumentado/ReporteDocumentado';

import EditarReporte from '../../components/EditarReportes/FormEditarReporte';
import EditarDocumentado from '../../components/EditarDocumentado/FormEditarDocumentado';

import EditarRegistro from '../../components/EditarRegistros/FormEditarRegistro';

import Registro from '../Registro/Registro';
import { getReportes , getReporte,getDocumentado,  getTransportistas, getLocalidedes } from '../../api/api.js';
import { withRouter } from 'react-router-dom';

const initialReport = {
   id: '',
   actualizacion: '',
   localidad: '',
   claim: '',
   transportista: '',
   reclamadoUSD: '',
   reclamadoMXN: '',
   excedente: '',
   estimado: '',
   rechazado: '',
   aceptado: '',
   cancelado: '',
   fechaEvento: '',
   fechaEmision: '',
   fecha1: '',
   fecha2: '',
   fecha3: '',
   fechaRespuesta: '',
   fechaAplicacion: '',
   fechaSolicitud: '',
   fechaEscalacion: '',
   area: '',
   fechaResolucion: '',
   estatus: '',
   observaciones: '',
   change: 0,
   eventoEmision: '',
   emisionHoy: '',
   escalacionResolucion: '',
   mayor45: '',


 };
 const initialDocumentado = {
    idD: '',
    localidadD: '',
    claimD: '',
    codigo: '',
    planta: '',
    numero: '',
    cantidad: '',
    fechaNotificacion: '',
    perdidaSinFlete: '',
    perdidaConFlete: '',
    documentacionFaltante: '',
    change: 0,
    areaD: '',
    estatusD: '',
  };


 const reporteReducer = (estado, action) => {
   switch (action.type) {
   case 'SET_REPORTE': {
     return {
       ...estado,
       id: action.payload.id.toString(),
       actualizacion: action.payload.actualizacion,
       localidad: action.payload.localidad,
       claim: action.payload.claim,
       tipo: action.payload.tipo,
       transportista: action.payload.transportista,
       reclamadoUSD: action.payload.reclamadoUSD,
       reclamadoMXN: action.payload.reclamadoMXN,
       excedente: action.payload.excedente,
       estimado: action.payload.estimado,
       rechazado: action.payload.rechazado,
       aceptado: action.payload.aceptado,
       cancelado: action.payload.cancelado,
       flete: action.payload.flete,
       fechaEvento: action.payload.fechaEvento,
       fechaEmision: action.payload.fechaEmision,
       fecha1: action.payload.fecha1,
       fecha2: action.payload.fecha2,
       fecha3: action.payload.fecha3,
       fechaAplicacion: action.payload.fechaAplicacion,
       fechaRespuesta: action.payload.fechaRespuesta,
       fechaSolicitud: action.payload.fechaSolicitud,
       fechaEscalacion: action.payload.fechaEscalacion,
       area: action.payload.area,
       fechaResolucion: action.payload.fechaResolucion,
       estatus: action.payload.estatus,
       observaciones: action.payload.observaciones,
       change:1,
       eventoEmision: action.payload.eventoEmision,
       emisionHoy: action.payload.emisionHoy,
       escalacionResolucion: action.payload.escalacionResolucion,
       mayor45: action.payload.mayor45,

     };
   }
   default: return estado;
   }
 }

 const documentadoReducer = (estadoDocumentado, action) => {
   switch (action.type) {

   case 'SET_DOCUMENTADO': {
     return {
       ...estadoDocumentado,
       idD: action.payload.id,
       localidadD: action.payload.localidad,
       claimD: action.payload.claim,
       codigo: action.payload.codigo,
       plantaD: action.payload.planta,
       numero: action.payload.numero,
       cantidad: action.payload.cantidad,
       fechaNotificacion: action.payload.fecha,
       perdidaSinFlete: action.payload.perdidaSinFlete,
       perdidaConFlete: action.payload.perdidaConFlete,
       documentacionFaltante: action.payload.documentacionFaltante,
       changeD: 1,
       areaD: action.payload.area,
       estatusD: action.payload.estatus,
     };
   }
   default: return estadoDocumentado;
   }
 }




function Home(props) {
  const {credentials,
    onLogOut,
    history,
    onClickReporte,
    onClickDocumentado,
    documentado,
    reporte, onRep, repToFalse, localidades,
    onDoc, docToFalse,
    transportistas, rutas, plantas, areas, tipos, descripciones,
    proveedores,
    maxPages, registros , currentPage, total, filtros, limite, onBusqueda, onPaginator,
    aceptadoGrafica,
    reclamado,
    proceso,
    canceladoGrafica,
    recuperado,
    rechazadoGrafica,
    onDescargarExcerRegistros,
    onDescargarExcerRegistro,
    registro,
    onClickRegistro,
    regToFalse,
    onReg,
    transportistasGrafica,
    updateRegistros

  } = props;
const [estado, dispatch] = useReducer(reporteReducer, initialReport);
const [estadoDocumentado, dispatchDocumentado] = useReducer(documentadoReducer, initialDocumentado);

const {
  id,
  actualizacion,
  localidad,
  claim,
  transportista,
  reclamadoUSD,
  reclamadoMXN,
  excedente,
  estimado,
  rechazado,
  aceptado,
  cancelado,
  fechaEvento,
  fechaEmision,
  fecha1,
  fecha2,
  fecha3,
  fechaRespuesta,
  fechaAplicacion,
  fechaSolicitud,
  fechaEscalacion,
  area,
  fechaResolucion,
  estatus,
  observaciones,
  change,
  eventoEmision,
  emisionHoy,
  escalacionResolucion,
  mayor45,
 } = estado ;

 const {
    idD ,
    localidadD,
    claimD,
    codigo,
    plantaD,
    numero,
    cantidad,
    fechaNotificacion,
    perdidaSinFlete,
    perdidaConFlete,
    documentacionFaltante,
    areaD,
    estatusD,
    changeD,

  } = estadoDocumentado;

  const handleClickReporte = (reporte_id) =>  {

    getReporte(credentials, reporte_id)
    .then((datos) =>{
      dispatch({
          type: 'SET_REPORTE',
          payload: datos.item,
        });

    })
    .catch((e) => {

    })
  }

  const handleClickReporteDocumentado = (reporte_id) =>  {
    getDocumentado(credentials, reporte_id)
    .then((datos) =>{
      dispatchDocumentado({
          type: 'SET_DOCUMENTADO',
          payload: datos.item,
        });

    })
    .catch((e) => {

    })
  }

  const handleEditRegistro = (item) =>  {
    updateRegistros(item);
    onClickRegistro(item);
  }

const handleClickEdit = () => {
  history.push("/home/editar_registro/");
}

const handleClickEditDocumentado = () => {
  history.push("/home/editar_documentado/");
}


  useEffect(()=>{

    if(estado.change == 1 ){
      onClickReporte(estado);
    }
  },[estado]);

  useEffect(()=>{
    if(estadoDocumentado.changeD == 1 ){
      onClickDocumentado(estadoDocumentado);
    }
  },[estadoDocumentado]);

  useEffect(()=>{
    if(onRep){
      repToFalse();
      history.push("/home/reporte/");
    }
  },[reporte]);

  useEffect(()=>{
      if(onDoc){
        docToFalse();
        history.push("/home/documentado/");
      }
  },[documentado]);


  useEffect(()=>{

    if(onReg){
      regToFalse();
      history.push("/home/registro/");
    }
  },[registro]);

    return (
        <Router>
            <div className="home">
                <div className="aside">
                    <LogoHome className="logoHome"></LogoHome>
                    <Sidebar
                    credentials = {credentials}
                    onLogOut = {onLogOut}

                    ></Sidebar>
                </div>
                <div className="main">
                    <Switch>
                        <Route path="/home/historial">
                            <Historial
                            credentials = {credentials}
                            maxPages = {maxPages}
                            registros = {registros}
                            currentPage = {currentPage}
                            total = {total}
                            localidades = {localidades}
                            transportistas = {transportistas}
                            rutas = {rutas}
                            plantas = {plantas}
                            tipos = {tipos}
                            descripciones = {descripciones}
                            filtros  = {filtros}
                            limite = {limite}
                            onBusqueda = {onBusqueda}
                            aceptado = {aceptadoGrafica}
                            reclamado = {reclamado}
                            proceso = {proceso}
                            cancelado = {canceladoGrafica}
                            recuperado = {recuperado}
                            rechazado = {rechazadoGrafica}
                            transportistasGrafica = {transportistasGrafica}
                            onDescargarExcerRegistros = {onDescargarExcerRegistros}
                            onClickRegistro = {onClickRegistro}
                             />
                        </Route>
                        <Route path="/home/nuevo_registro">
                            <NuevoRegistro
                            credentials = {credentials}
                            localidades = {localidades}
                            transportistas = {transportistas}
                            areas = {areas}
                            proveedores = {proveedores}
                            rutas = {rutas}
                            plantas = {plantas}
                            tipos = {tipos}
                            descripciones = {descripciones}

                            />
                        </Route>
                        <Route path="/home/usuarios">
                            <Usuarios
                            credentials = {credentials} />
                        </Route>
                        <Route path="/home/reportes">
                            <NuevoReporte
                            credentials = {credentials}
                            onClickReporte = {handleClickReporte}
                            onClickReporteDocumentado = {handleClickReporteDocumentado}
                            localidades = {localidades}
                            plantas = {plantas}
                            />
                        </Route>
                        <Route path="/home/registro">
                            <Registro
                            credentials = {credentials}
                            registro = {registro}
                            onDescargarExcerRegistro = {onDescargarExcerRegistro}
                            onClickEdit = {handleClickEdit}
                            />
                        </Route>
                        <Route path="/home/reclamo">
                            <Reclamo
                            credentials = {credentials}
                            onLogOut = {onLogOut}
                            />
                        </Route>
                        <Route path="/home/reporte">
                            <Reporte
                            credentials = {credentials}
                            reporte = {reporte}
                            onClickRegistro = {onClickRegistro}
                            />
                        </Route>

                        <Route path="/home/documentado">
                            <ReporteDocumentado
                            credentials = {credentials}
                            doc = {documentado}
                            onClickEdit = {handleClickEditDocumentado}
                            onClickRegistro = {onClickRegistro}
                            />
                        </Route>

                        <Route path="/home/editar_documentado">
                            <EditarDocumentado
                            credentials = {credentials}
                            doc = {documentado}
                            localidades = {localidades}
                            plantas = {plantas}
                            onEdit = {handleClickReporteDocumentado}
                            />
                        </Route>

                        <Route path="/home/editar_registro">
                            <EditarRegistro
                            credentials = {credentials}
                            registro = {registro}
                            localidades = {localidades}
                            transportistas = {transportistas}
                            areas = {areas}
                            proveedores = {proveedores}
                            rutas = {rutas}
                            plantas = {plantas}
                            onEditRegistro = {handleEditRegistro}
                            tipos = {tipos}
                            descripciones = {descripciones}
                            />
                        </Route>

                    </Switch>
                </div>
            </div>
        </Router>
    );
}

export default withRouter(Home);
