import React, { useState } from 'react';
import {
    TextField, Select, InputLabel,
    FormControl, MenuItem, Button,
    InputAdornment
} from '@material-ui/core';
import './FilterHistorial.scss'
import  Lupa from '../../img/lupa.js';
import {
    KeyboardDatePicker,
    MuiPickersUtilsProvider
} from '@material-ui/pickers';
import DateFnsUtils from '@date-io/date-fns';
import esLocale from "date-fns/locale/es";
import NumberFormat from 'react-number-format';
import PropTypes from 'prop-types';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import ExpandLessIcon from '@material-ui/icons/ExpandLess';
import RangePicker from 'react-range-picker';



function YearFormatCustom(props) {
  const { inputRef, onChange, ...other } = props;

  return (
    <NumberFormat
      {...other}
      getInputRef={inputRef}
      onValueChange={(values) => {
        onChange({
          target: {
            value: values.value,
          },
        });
      }}
      isNumericString
    />
  );
}

YearFormatCustom.propTypes = {
  inputRef: PropTypes.func.isRequired,
  name: PropTypes.string.isRequired,
  onChange: PropTypes.func.isRequired,
};

function FilterHistorial(props) {
  const {onBusqueda, localidades, transportistas, rutas, plantas, tipos, descripciones,filtros} = props;
    const [selectedDate, handleDateChange] = useState(new Date());
    const [state, setState] = useState(false);

    const [localidadFilter, setLocalidadFilter] = useState(filtros.localidad);
    const [plantaFilter, setPlantaFilter] = useState(filtros.planta);
    const [tipoFilter, setTipoFilter] = useState(filtros.tipo);
    const [descripcionFilter, setDescripcionFilter] = useState(filtros.descripcion);
    const [fechaEventoFilter, setFechaEventoFilter] = useState(filtros.fechaEvento);
    const [fechaEventoFilter2, setFechaEventoFilter2] = useState(filtros.fechaEvento2);

    const [transportistaFilter, setTransportistaFilter] = useState(filtros.transportista);

    const [fechaEmisionFilter, setFechaEmisionFilter] = useState(filtros.fechaEmision);
    const [fechaEmisionFilter2, setFechaEmisionFilter2] = useState(filtros.fechaEmision2);

    const [fechaRespuestaFilter, setFechaRespuestaFilter] = useState(filtros.fechaRespuesta);
    const [fechaRespuestaFilter2, setFechaRespuestaFilter2] = useState(filtros.fechaRespuesta2);

    const [fechaPagoFilter, setFechaPagoFilter] = useState(filtros.fechaPago);
    const [fechaPagoFilter2, setFechaPagoFilter2] = useState(filtros.fechaPago2);

    const [estatusFilter, setEstatusFilter] = useState(filtros.estatus);
    const [escaladoFilter, setEscaladoFilter] = useState(filtros.escalado);
    const [rutaFilter, setRutaFilter] = useState(filtros.ruta);
    const [anoEventoFilter, setAnoEventoFilter] = useState(filtros.anoEvento);
    const [anoAsignacionFilter, setAnoAsignacionFilter] = useState(filtros.anoAsignacion);
    const [anoDocumentacionFilter, setAnoDocumentacionFilter] = useState(filtros.anoDocumentacion);

    const [busquedaFilter, setBusquedaFilter] = useState(filtros.busqueda);


    function toggle() {
        setState(!state);
      }

    


    const handleBusqueda = () => {
      const datos = {
          localidad: localidadFilter,
          planta: plantaFilter,
          tipo: tipoFilter,
          descripcion: descripcionFilter,
          fechaEvento : fechaEventoFilter,
          fechaEvento2 : fechaEventoFilter2,

          transportista: transportistaFilter,

          fechaEmision: fechaEmisionFilter,
          fechaEmision2: fechaEmisionFilter2,

          fechaRespuesta: fechaRespuestaFilter,
          fechaRespuesta2: fechaRespuestaFilter2,

          fechaPago: fechaPagoFilter,
          fechaPago2: fechaPagoFilter2,

          estatus: estatusFilter,
          escalado: escaladoFilter,
          ruta: rutaFilter,
          anoEvento: anoEventoFilter,
          anoAsignacion: anoAsignacionFilter,
          anoDocumentacion: anoDocumentacionFilter,
          busqueda: busquedaFilter,
          pagina: 1,
      }
      onBusqueda(datos);
    }

      const ph = (t, s, e) => {
          const texto2 = () => {
            if(!!s && !!e){
              return (<div> <span className="date">{s.toLocaleDateString('es-MX')}</span>&nbsp;&nbsp; <b>-</b>&nbsp;<span className="date">{e.toLocaleDateString('es-MX')}</span> </div> );
            }else if (!!s) {
              return  (<div> <span className="date">{s.toLocaleDateString('es-MX')}</span>&nbsp;&nbsp;<b>-</b>&nbsp;<span className="date"></span> </div>)
            }else if (!!e) {
              return (<div>  <span className="date"></span>&nbsp;&nbsp;<b>-</b>&nbsp;<span className="date">{e.toLocaleDateString('es-MX')}</span> </div>)
            }else {
              return (<div> <span className="date">Fecha de {t}</span> </div>)
            }

          return (<span className="date">Fecha de {t}</span>);
          }

          return (
            <div className="default-placeholder">
                <div className="text">
                    <div className="dates-container" style={{ fontSize: '14px' }}>
                    {
                      texto2()
                    }
                    </div>
                </div>

                <div className="icon">
                  <div className="calendar-hooks">
                    <div className="hook"></div>
                    <div className="hook"></div>
                  </div>
                  <div className="date-dots">
                    <div className="dot"></div>
                    <div className="dot"></div>
                    <div className="dot"></div>
                    <div className="dot"></div>
                    <div className="dot"></div>
                  </div>
              </div>

            </div>
          )
        }

        function seleccionar(e) {
            let a = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.setAttribute('class', 'calendar');
          }

          function borrarFiltros(e,evento) {
            let a = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.setAttribute('class', 'calendar');
              if (evento === 'evento') {
                borrarFiltrosEvento();
              }else if (evento === 'emision') {
                borrarFiltrosEmision();
              }else if (evento === 'respuesta') {
                borrarFiltrosRespuesta();
              }else if (evento === 'pago') {
                borrarFiltrosPago();
              }
            }


        const footer = ( s ,  e , evento) => {
            const start = !!s ? s.toLocaleDateString('es-MX')  : '';
            const end = !!e ? e.toLocaleDateString('es-MX')  : '';

            return (
              <div className="default-footer">
                <div className="selected-dates">
                  <div className="date-heading">
                    Fecha Seleccionada
                  </div>

                  <div className="holder-wrapper">
                    <div className="date-holder ">
                      <div className="heading">
                        Desde
                      </div>

                      <div className="date">
                        {start}
                        <span className="time">  </span>
                      </div>

                    </div>
                    <div className="date-holder second">
                      <div className="heading">
                        Hasta
                      </div>
                      <div className="date">
                      {end} <span className="time">  </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="buttons">
                  <button className="select" onClick= {seleccionar}> Seleccionar </button>
                  <button className="select" onClick = {(eve) => {borrarFiltros(eve, evento) } } >Sin Filtro </button>
                </div>
              </div>
            )
          }


      const onDateChanges = (date, date2) => {
          if(date ){
            setFechaEventoFilter(date);
          }
          if(date2  ){
            setFechaEventoFilter2(date2);
          }

        }

        const onDateChangesEmision = (date, date2) => {
            if(date ){
              setFechaEmisionFilter(date);
            }
            if(date2  ){
              setFechaEmisionFilter2(date2);
            }

          }

          const onDateChangesRespuesta = (date, date2) => {
              if(date ){
                setFechaRespuestaFilter(date);
              }
              if(date2  ){
                setFechaRespuestaFilter2(date2);
              }

            }

            const onDateChangesPago = (date, date2) => {
                if(date ){
                  setFechaPagoFilter(date);
                }
                if(date2  ){
                  setFechaPagoFilter2(date2);
                }

              }

    function borrarFiltrosEvento() {
      setFechaEventoFilter('');
      setFechaEventoFilter2('');
      }

      function borrarFiltrosEmision() {
        setFechaEmisionFilter('');
        setFechaEmisionFilter2('');
        }

        function borrarFiltrosRespuesta() {
          setFechaRespuestaFilter('');
          setFechaRespuestaFilter2('');
          }
          function borrarFiltrosPago() {
            setFechaPagoFilter('');
            setFechaPagoFilter2('');
            }


    return (
        <div className="filterHistorial">
            <div className="row-space">

                <div className="title" onClick={toggle}>Filtros de busqueda
                {state ? <span style={{marginLeft: '10px'}}><ExpandLessIcon /></span> : <span style={{marginLeft: '10px'}}><ExpandMoreIcon /></span>}
                </div>

                <div className="column">
                    <div className="column-lg">
                        <TextField id="Busqueda" label="Busqueda" className="textField" variant="outlined" size="small"
                        value = {busquedaFilter}
                          onChange={(e) => {setBusquedaFilter(e.target.value);}}
                            InputProps={{
                                startAdornment: (
                                    <InputAdornment position="start">
                                        <Lupa />
                                    </InputAdornment>
                                ),
                            }} />
                    </div>
                    <div className="column-sm">

                        <Button variant="contained" color="secondary" className="button"
                        onClick={() => handleBusqueda()}>
                            Buscar
                        </Button>
                    </div>

                </div>
            </div>

            <div className="column" style={{display: state ? 'block' : 'none' }}>
                <div className="row">
                    <div className="column">
                        <FormControl variant="outlined" className="textField" size="small">
                            <InputLabel htmlFor="outlined-localidad">Localidad</InputLabel>
                            <Select
                                labelId="outlined-localidad"
                                id="localidad"
                                label="Localidad"
                                value={localidadFilter}
                                onChange={(e) => {setLocalidadFilter(e.target.value)}}
                            >

                            {(!!localidades) ?  localidades.map((localidad) =>  {
                                  return(<MenuItem value={localidad.localidad}> {localidad.localidad}</MenuItem>)
                            }) : ('')
                            }
                            <MenuItem value=''>Sin Filtro</MenuItem>

                            </Select>
                        </FormControl>
                    </div>
                    <div className="column">
                        <FormControl variant="outlined" className="textField" size="small">
                            <InputLabel htmlFor="outlined-planta">Planta</InputLabel>
                            <Select
                                labelId="outlined-planta"
                                id="planta"
                                label="Planta"
                                value={plantaFilter}
                                onChange={(e) => {setPlantaFilter(e.target.value)}}
                            >
                            { (!!plantas) ?  plantas.map((planta) =>  {
                                  return(<MenuItem value={planta.planta}> {planta.planta}</MenuItem>)
                            }) : ('')
                            }
                            <MenuItem value=''>Sin Filtro</MenuItem>
                            </Select>
                        </FormControl>
                    </div>
                    <div className="column">
                        <FormControl variant="outlined" className="textField" size="small">
                            <InputLabel htmlFor="outlined-tipo-evento">Tipo de evento</InputLabel>
                            <Select
                                labelId="outlined-tipo-evento"
                                id="tipoEvento"
                                label="Tipo de evento"
                                value={tipoFilter}
                                onChange={(e) => {setTipoFilter(e.target.value)}}
                            >
                            { (!!tipos) ?  tipos.map((tipo) =>  {
                                  return(<MenuItem value={tipo.tipo}> {tipo.tipo}</MenuItem>)
                            }) : ('')
                            }
                            <MenuItem value=''>Sin Filtro</MenuItem>
                                                    </Select>
                        </FormControl>
                    </div>
                    <div className="column">
                        <FormControl variant="outlined" className="textField" size="small">
                            <InputLabel htmlFor="outlined-desc-evento">Descripción del evento</InputLabel>
                            <Select
                                labelId="outlined-desc-evento"
                                id="descEvento"
                                label="Descripción del evento"
                                value={descripcionFilter}
                                onChange={(e) => {setDescripcionFilter(e.target.value)}}
                            >
                            { (!!descripciones) ?  descripciones.map((descripcion) =>  {
                                  return(<MenuItem value={descripcion.descripcion}> {descripcion.descripcion}</MenuItem>)
                            }) : ('')
                            }
                            <MenuItem value=''>Sin Filtro</MenuItem>
                            </Select>
                        </FormControl>
                    </div>
                    <div className="column-sm">

                      <RangePicker onDateSelected={onDateChanges}
                                format="dd/MM/yyyy"
                                closeOnSelect="true"
                                placeholder = {
                                  ((a) => {
                                    return ph('evento',fechaEventoFilter, fechaEventoFilter2);
                                  })
                                }
                                footer = {
                                  ((a) => {
                                    return footer(fechaEventoFilter, fechaEventoFilter2, 'evento');
                                  })
                                }
                                id="fechaEvento"
                                />

                    </div>

                </div>


                <div className="row">
                <div className="column">
                    <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-transportista">Transportista</InputLabel>
                        <Select
                            labelId="outlined-transportista"
                            id="transportista"
                            label="Transportista"
                            value={transportistaFilter}
                            onChange={(e) => {setTransportistaFilter(e.target.value)}}
                        >
                        {(!!transportistas) ? transportistas.map((transportista) =>  {
                              return(<MenuItem value={transportista.transportista}> {transportista.transportista}</MenuItem>)
                        }) : ('')
                        }
                        <MenuItem value=''>Sin Filtro</MenuItem>
                        </Select>
                    </FormControl>
                </div>
                <div className="column-sm">

                  <RangePicker onDateSelected={onDateChangesEmision}
                            format="dd/MM/yyyy"
                            closeOnSelect="true"
                            placeholder = {
                              ((a) => {
                                return ph('emision',fechaEmisionFilter, fechaEmisionFilter2);
                              })
                            }
                            footer = {
                              ((a) => {
                                return footer(fechaEmisionFilter, fechaEmisionFilter2, 'emision');
                              })
                            }

                            id="fechaEmision"
                            />

                </div>

                <div className="column-sm">

                  <RangePicker onDateSelected={onDateChangesRespuesta}
                            format="dd/MM/yyyy"
                            closeOnSelect="true"
                            placeholder = {
                              ((a) => {
                                return ph('respuesta',fechaRespuestaFilter, fechaRespuestaFilter2);
                              })
                            }
                            footer = {
                              ((a) => {
                                return footer(fechaRespuestaFilter, fechaRespuestaFilter2, 'respuesta');
                              })
                            }
                            id="fechaRespuesta"
                            />

                </div>

                <div className="column-sm">

                  <RangePicker onDateSelected={onDateChangesPago}
                            format="dd/MM/yyyy"
                            closeOnSelect="true"
                            placeholder = {
                              ((a) => {
                                return ph('pago',fechaPagoFilter, fechaPagoFilter2);
                              })
                            }
                            footer = {
                              ((a) => {
                                return footer(fechaPagoFilter, fechaPagoFilter2, 'pago');
                              })
                            }
                            id="fechaPago"
                            />

                </div>



                </div>
                <div className="row">

                    <div className="column">
                        <FormControl variant="outlined" className="textField" size="small">
                            <InputLabel htmlFor="outlined-estatus">Estatus</InputLabel>
                            <Select
                                labelId="outlined-estatus"
                                id="estatus"
                                label="Estatus"
                                value={estatusFilter}
                                onChange={(e) => {setEstatusFilter(e.target.value)}}
                            >
                            <MenuItem value={'En Proceso'}>En Proceso</MenuItem>
                            <MenuItem value={'Aceptado'}>Aceptado</MenuItem>
                            <MenuItem value={'Rechazado'}>Rechazado</MenuItem>
                            <MenuItem value={'Pagado'}>Pagado</MenuItem>
                            <MenuItem value={'Cancelado'}>Cancelado</MenuItem>
                            <MenuItem value=''>Sin Filtro</MenuItem>
                            </Select>
                        </FormControl>
                    </div>
                    <div className="column">
                        <FormControl variant="outlined" className="textField" size="small">
                            <InputLabel htmlFor="outlined-escalado">Escalado</InputLabel>
                            <Select
                                labelId="outlined-escalado"
                                id="escalado"
                                label="Escalado"
                                value={escaladoFilter}
                                onChange={(e) => {setEscaladoFilter(e.target.value)}}
                            >
                            <MenuItem value={'Si'}>Si</MenuItem>
                            <MenuItem value={'No'}>No</MenuItem>
                            <MenuItem value=''>Sin Filtro</MenuItem>
                            </Select>
                        </FormControl>
                    </div>
                    <div className="column">
                        <FormControl variant="outlined" className="textField" size="small">
                            <InputLabel htmlFor="outlined-desc-ruta">Ruta</InputLabel>
                            <Select
                                labelId="outlined-ruta"
                                id="ruta"
                                label="Ruta"
                                value={rutaFilter}
                                onChange={(e) => {setRutaFilter(e.target.value)}}
                            >
                            { (!!rutas) ? rutas.map((ruta) =>  {
                                  return(<MenuItem value={ruta.ruta}> {ruta.ruta}</MenuItem>)
                            }) : ('')
                            }
                                <MenuItem value=''>Sin Filtro</MenuItem>
                            </Select>
                        </FormControl>
                    </div>
                    <div className="column">
                       
                    </div>
                </div>

                <div className="row">

                <div className="column">
                    <TextField id="anoEvento" label="Año de evento" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: YearFormatCustom,
                        }}
                        value={anoEventoFilter}
                        onChange={(e) => {setAnoEventoFilter(e.target.value);}}
                         />

                    </div>
                    <div className="column">
                    <TextField id="anoAsignacion" label="Año de Asignacion" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: YearFormatCustom,
                        }}
                        value={anoAsignacionFilter}
                        onChange={(e) => {setAnoAsignacionFilter(e.target.value);}}
                         />
                    </div>
                    <div className="column">
                    <TextField id="anoDocumentacion" label="Año de Documentacion" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: YearFormatCustom,
                        }}
                        value={anoDocumentacionFilter}
                        onChange={(e) => {setAnoDocumentacionFilter(e.target.value);}}
                         />
                    </div>
                    <div className="column">
                       
                    </div>
                </div>

            </div>
        </div>
    )
}

export default FilterHistorial
