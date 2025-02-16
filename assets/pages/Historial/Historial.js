import React, {useState, useEffect}from 'react'

import FilterHistorial from '../../components/FilterHistorial/FilterHistorial'
import HistorialRegistros from '../../components/HistorialRegistros/HistorialRegistros'
import Graficas from '../../components/Graficas/Graficas'

import {getExcelRegistros ,deleteFile } from '../../api/api.js';

import './Historial.scss'

function Historial(props) {

  const {credentials, maxPages, registros ,currentPage, total,
  localidades, plantas, transportistas, rutas, tipos, descripciones,
  filtros , limite, onBusqueda,
  aceptado,
  reclamado,
  proceso,
  cancelado,
  recuperado,
  rechazado,
  transportistasGrafica,
onDescargarExcerRegistros,
onClickRegistro } = props;

  const [filter, setFilter] = useState({

      localidad: '',
      planta: '',
      tipo: '',
      descripcion: '',
      fechaEvento :'',
      transportista: '',
      fechaEmision: '',
      fechaRespuesta: '',
      fechaPago: '',
      estatus: '',
      escalado: '',
      ruta: '',
      busqueda: '',
      pagina: 1,

  })
  const handleBusqueda = (datos) => {
    setFilter(datos);
    onBusqueda(datos);
  }

  const handlePaginator = (pagina) => {
     let filt = { ... filtros, pagina:pagina};
    return  onPaginator(filt);
  }
    return (
        <div className="historial">
            <FilterHistorial
            onBusqueda = {handleBusqueda}
            localidades = {localidades}
            transportistas = {transportistas}
            rutas = {rutas}
            plantas = {plantas}
            tipos = {tipos}
            descripciones = {descripciones}

            filtros = {filtros}


            ></FilterHistorial>

            <Graficas
            aceptado = {aceptado}
            reclamado = {reclamado}
            proceso = {proceso}
            cancelado = {cancelado}
            recuperado = {recuperado}
            rechazado = {rechazado}
            total = {total}
            transportistasGrafica = {transportistasGrafica}
            ></Graficas>

            <HistorialRegistros title="Resultados de busqueda"
            registros = {registros}
            currentPage = {currentPage}
            maxPages = {maxPages}
            total = {total}
            limite = {limite}
            onDescargarExcerRegistros = {onDescargarExcerRegistros}
            credentials = {credentials}
            onClickRegistro = {onClickRegistro}
            filtros = {filtros}



            ></HistorialRegistros>

        </div>
    )
}

export default Historial
