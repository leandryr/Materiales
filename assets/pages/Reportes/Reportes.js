import React, { useState, useEffect} from 'react';
import { Tabs, Tab,   TextField, Select, InputLabel,
  FormControl, MenuItem, Button,
  InputAdornment } from '@material-ui/core';
import FormNuevoReporte from '../../components/FormReportes/FormNuevoReporte';
import DialogProvider from '../../context/DialogProvider';
import ReporteSemanal from '../../components/ReporteSemanal/ReporteSemanal';

import ReporteDocumentados from '../../components/ReporteDocumentados/ReporteDocumentados';
import RangePicker from 'react-range-picker';
import {
    KeyboardDatePicker,
    MuiPickersUtilsProvider
} from '@material-ui/pickers';

import clsx from "clsx";
import format from "date-fns/format";
import isValid from "date-fns/isValid";
import isSameDay from "date-fns/isSameDay";
import endOfWeek from "date-fns/endOfWeek";
import startOfWeek from "date-fns/startOfWeek";
import isWithinInterval from "date-fns/isWithinInterval";
import DateFnsUtils from '@date-io/date-fns';
import { createStyles } from "@material-ui/styles";
import { IconButton, withStyles } from "@material-ui/core";
import esLocale from "date-fns/locale/es";



import './Reportes.scss';
import { getReportes, getDocumentados  } from '../../api/api.js';


const styles = createStyles(theme => ({
  dayWrapper: {
    position: "relative",
  },
  day: {
    width: 36,
    height: 36,
    fontSize: theme.typography.caption.fontSize,
    margin: "0 2px",
    color: "inherit",
  },
  customDayHighlight: {
    position: "absolute",
    top: 0,
    bottom: 0,
    left: "2px",
    right: "2px",
    border: `1px solid ${theme.palette.secondary.main}`,
    borderRadius: "50%",
  },
  nonCurrentMonthDay: {
    color: theme.palette.text.disabled,
  },
  highlightNonCurrentMonthDay: {
    color: "#676767",
  },
  highlight: {
    background: theme.palette.primary.main,
    color: theme.palette.common.white,
  },
  firstHighlight: {
    extend: "highlight",
    borderTopLeftRadius: "50%",
    borderBottomLeftRadius: "50%",
  },
  endHighlight: {
    extend: "highlight",
    borderTopRightRadius: "50%",
    borderBottomRightRadius: "50%",
  },
}));


function NuevoReporte(props) {
  const [semanas, setSemanas] = useState(null);
  const [documentados, setDocumentados] = useState(null);

    const [selectedTab, setSelectedTab] = useState(0);
    const [init, setInit] = useState(0);

    const [fechaFilter, setFechaFilter] = useState(null);
    const [rangoFilter, setRangoFilter] = useState(null);
    const [rangoFilter2, setRangoFilter2] = useState(null);

    const [estatusFilter, setEstatusFilter] = useState('no');
    const [tipoReporteFilter, setTipoReporteFilter] = useState('no');
    const [fechaLaber, setFechaLabel] = useState(null);




    const {credentials, onClickReporte,onClickReporteDocumentado, localidades, plantas, transportistas, classes} = props;
    const handleChange = (event, newValue) => {
        setSelectedTab(newValue);
    }
    useEffect(()=>{
      if (init === 0 ) {
        const datos = {
            //fechaEvento1: '',
            //fechaEvento2: '',
            estatus: 'no',
            tipoReporte: 'no',

        }
        getReportes(credentials,datos)
        .then((datos) => {
          setSemanas(datos.validation.items)
        })
        .catch();

        handleNewReport();
        setInit(1);
      }
    },[]);

    const handleBusquedaFilter = () => {
      const datos = {
        //  fechaEvento1: rangoFilter,
        //  fechaEvento2: rangoFilter2,
          estatus: estatusFilter,
          tipoReporte: tipoReporteFilter,

      }
      getReportes(credentials,datos)
      .then((datos) => {
        setSemanas(datos.validation.items)
      })
      .catch();

    }

    const handleNewReport = () => {

      getDocumentados(credentials)
      .then((datos) => {
        setDocumentados(datos.items)
      })
      .catch();

    }


        function borrarFiltrosEvento() {
          setRangoFilter('');
          setRangoFilter2('');
          }

  function handleWeekChange(date) {
    if (!!date) {
      let dateClone = date;
      const start = startOfWeek(dateClone, {weekStartsOn: 5});
      const end = endOfWeek(dateClone, {weekStartsOn: 5});
      setFechaFilter(start);

      setRangoFilter(start);
      setRangoFilter2(end);
    }else {
      borrarFiltrosEvento();
    }
  }


  function renderWrappedWeekDay(date, selectedDate, dayInCurrentMonth){
    let dateClone = date;
    let selectedDateClone = selectedDate;

    const start = startOfWeek(selectedDateClone, {weekStartsOn: 5});
    const end = endOfWeek(selectedDateClone, {weekStartsOn: 5});

    const dayIsBetween = isWithinInterval(dateClone, { start, end });
    const isFirstDay = isSameDay(dateClone, start);
    const isLastDay = isSameDay(dateClone, end);

    const wrapperClassName = clsx({
      [classes.highlight]: dayIsBetween,
      [classes.firstHighlight]: isFirstDay,
      [classes.endHighlight]: isLastDay,
    });

    const dayClassName = clsx(classes.day, {
      [classes.nonCurrentMonthDay]: !dayInCurrentMonth,
      [classes.highlightNonCurrentMonthDay]: !dayInCurrentMonth && dayIsBetween,
    });

    return (
      <div className={wrapperClassName}>
        <IconButton className={dayClassName}>
          <span> {format(dateClone, "d")} </span>
        </IconButton>
      </div>
    );
  };

  function formatWeekSelectLabel(date, invalidLabel){
    let dateClone = date;

    let start = !!rangoFilter ? rangoFilter.toLocaleDateString('es-MX')  : '';
    let end = !!rangoFilter2 ? rangoFilter2.toLocaleDateString('es-MX')  : '';

    return ('' + start +'-' + end ) ;

  };
  esLocale.options.weekStartsOn = 0; // 0-6 (Sunday-Saturday)



    return (
        <DialogProvider>
            <div className="reportes">
            <div>
              <div className="row-space">
                  <div>
                    <Tabs value={selectedTab} onChange={handleChange}>
                        <Tab label="En documentacion"></Tab>
                        <Tab label="Reporte semanal"></Tab>
                    </Tabs>
                  </div>

                  {selectedTab === 1 &&
                    <div className="column">

                      {/*
                          <div className="column">

                          <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                              <KeyboardDatePicker
                                  className="textField"
                                  variant="inline"
                                  format="dd/MM/yyyy"
                                  autoOk = 'true'
                                  id="date-picker-inline"
                                  inputVariant="outlined"
                                  size="small"
                                  label="Fecha de emision"
                                  InputAdornmentProps={{ position: "start" }}
                                  value={fechaFilter}
                                  onChange={(value) => handleWeekChange(value)}
                                  disableFuture = 'true'
                                  renderDay={renderWrappedWeekDay}
                                  labelFunc={formatWeekSelectLabel}
                              />
                          </MuiPickersUtilsProvider>

                          </div>
                      */}



                      <div className="column">
                      <FormControl variant="outlined" className="textField" size="small" >
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
                                  <MenuItem value={'no'}>Sin Filtro</MenuItem>


                              </Select>
                          </FormControl>

                      </div>
                      <div className="column">

                      <FormControl variant="outlined" className="textField" size="small">
                              <InputLabel htmlFor="outlined-reporte">Reporte</InputLabel>
                              <Select
                                  labelId="outlined-reporte"
                                  id="reporte"
                                  label="Reporte"
                                  value={tipoReporteFilter}
                                  onChange={(e) => {setTipoReporteFilter(e.target.value)}}
                                >
                                  <MenuItem value={'mayores'}>Mayores a 45 días</MenuItem>
                                  <MenuItem value={'menores'}>Menores a 45 días</MenuItem>
                                  <MenuItem value={'no'}>Sin Filtro</MenuItem>


                              </Select>
                          </FormControl>

                      </div>

                      <div  >
                          <Button variant="contained" color="secondary" className="button"
                          onClick={() => handleBusquedaFilter()}>
                              Buscar
                          </Button>
                      </div>

                    </div>
                  }



              </div>
            </div>


                {selectedTab === 0 &&
                  <div>
                  <FormNuevoReporte
                  credentials = {credentials}
                  localidades= {localidades}
                  transportistas = {transportistas}
                  plantas = {plantas}
                  onNewReport = {handleNewReport}
                   />

                         <ReporteDocumentados
                         credentials = {credentials}
                       documentados= {documentados}
                       onClickReporteDocumentado = {onClickReporteDocumentado}/>

                   </div>
                 }
                {selectedTab === 1 && <div>
                  {(!!semanas) ? (semanas.map((semana)=>{
                    return (
                      <div key = {semana.semana}>
                      <ReporteSemanal
                      credentials = {credentials}
                    semana= {semana}
                    onClickReporte = {onClickReporte}
                    estatus = {estatusFilter}
                    tipoReporte = {tipoReporteFilter}/>
                    </div>
                  );
                  })
                ) : ('')}
                </div>
                }

            </div>
        </DialogProvider>
    )
}

export default withStyles(styles)( NuevoReporte)
