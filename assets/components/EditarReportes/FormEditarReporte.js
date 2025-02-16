import React, { useState } from 'react';
import './FormEditarReporte.scss';
import {
    TextField, Select, InputLabel,
    FormControl, MenuItem, Button
} from '@material-ui/core';
import {
    KeyboardDatePicker,
    MuiPickersUtilsProvider
} from '@material-ui/pickers';
import DateFnsUtils from '@date-io/date-fns';
import esLocale from "date-fns/locale/es";
import Autocomplete from '@material-ui/lab/Autocomplete';
import InputAdornment from '@material-ui/core/InputAdornment';
import NumberFormat from 'react-number-format';
import PropTypes from 'prop-types';
import { withRouter } from 'react-router-dom';
import SvgIcon from "@material-ui/core/SvgIcon";
import  Regresar  from '../../img/back_button.js';




import { editReport  } from '../../api/api.js';

function NumberFormatCustom(props) {
  const { inputRef, onChange, ...other } = props;

  return (
    <NumberFormat
      {...other}
      getInputRef={inputRef}
      onValueChange={(values) => {
        onChange({
          target: {
            name: props.name,
            value: values.value,
          },
        });
      }}
      thousandSeparator
      isNumericString
      prefix="$"
    />
  );
}

NumberFormatCustom.propTypes = {
  inputRef: PropTypes.func.isRequired,
  name: PropTypes.string.isRequired,
  onChange: PropTypes.func.isRequired,
};


function FormSiniestros(props) {
  const {history, credentials, localidades, transportistas,reporte, onEdit} = props;

  const [reporte_id, setReporteId] = useState(reporte.id);

  const [localidadField, setLocalidadField] = useState(reporte.localidad);
  const [claim, setClaim] = useState(reporte.claim);
  const [transportista, setTransportista] = useState(reporte.transportista);
  const [tipo, setTipo] = useState(reporte.tipo);
  const [reclamadoUSD, setReclamadoUSD] = useState(reporte.reclamadoUSD);
  const [reclamadoMXN, setreclamadoMXN] = useState(reporte.reclamadoMXN);

  const [excedenteMXN, setExcedente] = useState(reporte.excedente);
  const [estimadoMXN, setEstimado] = useState(reporte.estimado);
  const [rechazadoMXN, setRechazado] = useState(reporte.rechazado);
  const [aceptadoMXN, setAceptado] = useState(reporte.aceptado);

  const [canceladoMXN, setCancelado] = useState(reporte.cancelado);
  const [flete, setFlete] = useState(reporte.flete);

  const [fechaEvento, setFechaEvento] = useState(reporte.fechaEvento);
  const [fechaEmision, setFechaEmision] = useState(reporte.fechaEmision);
  const [fecha1, setFecha1] = useState(reporte.fecha1);
  const [fecha2, setFecha2] = useState(reporte.fecha2);
  const [fecha3, setFecha3] = useState(reporte.fecha3);
  const [fechaRespuesta, setFechaRespuesta] = useState(reporte.fechaRespuesta);
  const [fechaSolicitud, setFechaSolicitud] = useState(reporte.fechaSolicitud);
  const [fechaAplicacion, setFechaAplicacion] = useState(reporte.fechaAplicacion);
  const [fechaEscalacion, setFechaEscalcaion] = useState(reporte.fechaEscalacion);
  const [fechaResolucion, setFechaResolucion] = useState(reporte.fechaResolucion);


  const [area, setArea] = useState(reporte.area);
  const [estatus, setEstatus] = useState(reporte.estatus);
  const [observaciones, setObservaciones] = useState(reporte.observaciones);

  const handleAutoCompleteLocalidad = (event, newValue) => {
    setLocalidadField(newValue);
  }

  const handleAutoCompleteTransportista = (event, newValue) => {
    setTransportista(newValue);

  }

  const reset = () => {


  }
    const handleEditReporte = () => {

      const data = {
        id: reporte_id,
        localidad: localidadField,
        claim: claim,
        transportista: transportista,
        tipo: tipo,
        reclamadoUSD:reclamadoUSD,
        reclamadoMXN: reclamadoMXN,
        excedenteMXN: excedenteMXN,
        estimadoMXN: estimadoMXN,
        rechazadoMXN: rechazadoMXN,
        aceptadoMXN: aceptadoMXN,
        canceladoMXN: canceladoMXN,
        flete: flete,
        fechaEvento: fechaEvento,
        fechaEmision: fechaEmision,
        fecha1: fecha1,
        fecha2: fecha2,
        fecha3: fecha3,
        fechaRespuesta: fechaRespuesta,
        fechaSolicitud: fechaSolicitud,
        fechaAplicacion: fechaAplicacion,
        fechaEscalacion:fechaEscalacion,
        fechaResolucion: fechaResolucion,
        area: area,
        estatus: estatus,
        observaciones:observaciones
      };

      editReport(credentials,data)
      .then((datos) => {
        onEdit(reporte_id);
      })
      .catch((e) =>{

      });
    }
    const handleListItemClick = (url) => {
        history.push('/home/reporte');
    };

    return (
      <div className="editar_reporte">

      <div className="content-icon-header" >

          <div onClick={() => handleListItemClick('historial')}>
              <span >
              <SvgIcon component={Regresar} viewBox="0 0 30 30" />
               Regresar
              </span>
          </div>
      </div>

        <form className="formSiniestros" noValidate autoComplete="off">
            <div className="row">
            <div className="column">
                  <Autocomplete
                    id="free-solo-demo"
                    value={localidadField}
                    className="textField"
                    onChange={handleAutoCompleteLocalidad}
                    options={(!!localidades)  ? localidades.map((localidad) => localidad.localidad) : ('')}
                    renderInput={(params) => (
                      <TextField  {...params} id="localidad" label="Localidad" className="textField" variant="outlined" size="small"
                      value={localidadField}
                      onChange={(e) => {setLocalidadField(e.target.value);}} />
                    )}
                  />


                    </div>
                    <div className="column">
                    <TextField id="ref_claim" label="Ref. Claim" className="textField" variant="outlined" size="small"
                    value = {claim}
                    onChange = {(e) => {setClaim(e.target.value);}}
                    />
                    </div>
                    <div className="column">
                    <Autocomplete
                      id="transportistaAutocomplete"
                      className="textField"
                      value={transportista}
                      onChange={handleAutoCompleteTransportista}
                      options={(!!transportistas) ? transportistas.map((transpor) => transpor.transportista): ('')}
                      renderInput={(params) => (
                        <TextField  {...params} id="transportista" label="Transportista/Responsable" className="textField" variant="outlined" size="small"
                        value={transportista}
                        onChange={(e) => {setTransportista(e.target.value);}} />
                      )}
                    />


                    </div>
                    <div className="column">
                        <TextField id="tipo_dano" label="Tipo de daño" className="textField" variant="outlined" size="small"
                        value = {tipo}
                        onChange = { (e) => {setTipo(e.target.value);}}
                        />
                    </div>
            </div>
            <div className="row">
                <div className="column">
                    <div className="column">

                        <TextField id="MontoReclamadoUSD" label="Monto reclamado USD" className="textField" variant="outlined" size="small"
                        value = {reclamadoUSD}
                        onChange = { (e) => {setReclamadoUSD(e.target.value);}}
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        />
                    </div>
                    <div className="column">
                        <TextField id="MontoReclamadoMXN" label="Monto reclamado MXN" className="textField" variant="outlined" size="small"
                        value = {reclamadoMXN}
                        onChange = { (e) => {setreclamadoMXN(e.target.value);}}
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                         />
                    </div>
                </div>
                <div className="column">
                <div className="column">
                        <TextField id="MontoExcedente" label="Monto excedente de contrato MXN" className="textField" variant="outlined" size="small"
                        value = {excedenteMXN}
                        onChange = { (e) => {setExcedente(e.target.value);}}
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        />
                    </div>
                    <div className="column">
                        <TextField id="MontoEstimadoRecuperado" label="Monto estimado de recuperación MXN" className="textField" variant="outlined" size="small"
                        value = {estimadoMXN}
                        onChange = { (e) => {setEstimado(e.target.value);}}
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                         />
                    </div>

                </div>
            </div>

            <div className="row">
                <div className="column">
                      <div className="column">
                            <TextField id="MontoRechazado" label="Monto rechazado MXN" className="textField" variant="outlined" size="small"
                            value = {rechazadoMXN}
                            onChange = { (e) => {setRechazado(e.target.value);}}
                            InputProps={{
                              inputComponent: NumberFormatCustom,
                            }}
                            />
                        </div>
                        <div className="column">
                            <TextField id="MontoAceptado" label="Monto aceptado MXN" className="textField" variant="outlined" size="small"
                            value = {aceptadoMXN}
                            onChange = { (e) => {setAceptado(e.target.value);}}
                            InputProps={{
                              inputComponent: NumberFormatCustom,
                            }}
                             />
                        </div>
                    </div>
                <div className="column">
                    <div className="column">
                        <TextField id="MontoCancelado" label="Monto cancelado MXN" className="textField" variant="outlined" size="small"
                        value = {canceladoMXN}
                        onChange = { (e) => {setCancelado(e.target.value);}}
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                         />
                    </div>
                    <div className="column">
                        <TextField id="FleteNoIncluido" label="Flete no incluido en reclamo" className="textField" variant="outlined" size="small"
                        value = {flete}
                        onChange = { (e) => {setFlete(e.target.value);}}
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        />
                    </div>
                </div>
            </div>

            <div className="row ">
                <div className="column">
                    <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                        <KeyboardDatePicker
                            className="textField"
                            disableToolbar
                            variant="inline"
                            format="dd/MM/yyyy"
                            autoOk = 'true'
                            id="date-picker-inline"
                            inputVariant="outlined"
                            size="small"
                            label="Fecha de evento"
                            InputAdornmentProps={{ position: "start" }}
                            value={fechaEvento}
                            onChange={(value) => setFechaEvento(value)}
                        />
                    </MuiPickersUtilsProvider>
                </div>

                <div className="column">
                    <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale} >
                        <KeyboardDatePicker
                            className="textField"
                            disableToolbar
                            variant="inline"
                            format="dd/MM/yyyy"
                            autoOk = 'true'
                            id="date-picker-inline"
                            inputVariant="outlined"
                            size="small"
                            label="Fecha de emisión"
                            InputAdornmentProps={{ position: "start" }}
                            value={fechaEmision}
                            onChange={(value) => setFechaEmision(value) }
                        />
                    </MuiPickersUtilsProvider>
                </div>


              <div className="column ">
                {
                  reporte.mayor45 ? (<p className="texto45 ">-Reporte de mas de 45 días-</p>) : (<p className="texto45 ">-Reporte de menos de 45 días-</p> )
                }

              </div>
              <div className="column">
              </div>

              <div className="column">

              </div>


          </div>


          {
              reporte.mayor45  ? (
                <div>
                  <div className="row">

                          <div className="column">
                              <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                                  <KeyboardDatePicker
                                  className="datePicker"
                                      disableToolbar
                                      variant="inline"
                                      format="dd/MM/yyyy"
                                      autoOk = 'true'
                                      id="date-picker-inline"
                                      inputVariant="outlined"
                                      size="small"
                                      label="Fecha 1er notificación a RM"
                                      InputAdornmentProps={{ position: "start" }}
                                      value={fecha1}
                                      onChange={(value) => setFecha1(value) }
                                  />
                              </MuiPickersUtilsProvider>
                          </div>

                          <div className="column">
                              <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                                  <KeyboardDatePicker
                                  className="datePicker"
                                      disableToolbar
                                      variant="inline"
                                      format="dd/MM/yyyy"
                                      autoOk = 'true'
                                      id="date-picker-inline"
                                      inputVariant="outlined"
                                      size="small"
                                      label="Fecha 2da notificación a RM"
                                      InputAdornmentProps={{ position: "start" }}
                                      value={fecha2}
                                      onChange={(value) => setFecha2(value) }
                                  />
                              </MuiPickersUtilsProvider>
                          </div>
                          <div className="column">
                              <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                                  <KeyboardDatePicker
                                  className="datePicker"
                                      disableToolbar
                                      variant="inline"
                                      format="dd/MM/yyyy"
                                      autoOk = 'true'
                                      id="date-picker-inline"
                                      inputVariant="outlined"
                                      size="small"
                                      label="Fecha 3ra notificación a RM"
                                      InputAdornmentProps={{ position: "start" }}
                                      value={fecha3}
                                      onChange={(value) => setFecha3(value) }
                                  />
                              </MuiPickersUtilsProvider>
                          </div>

                          <div className="column">
                            <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                              <KeyboardDatePicker
                              className="datePicker"

                              disableToolbar
                              variant="inline"
                              format="dd/MM/yyyy"
                              autoOk = 'true'
                              id="date-picker-inline"
                              inputVariant="outlined"
                              size="small"
                              label="Fecha de escalación"
                              InputAdornmentProps={{ position: "start" }}
                              value={fechaEscalacion}
                              onChange={(value) => setFechaEscalcaion(value) }
                              />
                            </MuiPickersUtilsProvider>
                          </div>

                          <div className="column">
                            <TextField id="area_escalacion" label="Área de escalación/Responsable" className="textField" variant="outlined" size="small"
                            value = {area}
                            onChange = { (e) => setArea(e.target.value)}
                             />
                          </div>

                  </div>
                  <div className="row">
                        <div className="column">
                        <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                                <KeyboardDatePicker
                                className="datePicker"

                                    disableToolbar
                                    variant="inline"
                                    format="dd/MM/yyyy"
                                    autoOk = 'true'
                                    id="date-picker-inline"
                                    inputVariant="outlined"
                                    size="small"
                                    label="Fecha de resolución"
                                    InputAdornmentProps={{ position: "start" }}
                                    value={fechaResolucion}
                                    onChange={(value) => setFechaResolucion(value) }
                                />
                            </MuiPickersUtilsProvider>
                        </div>
                        <div className="column">
                        <FormControl variant="outlined" className="textField" size="small">
                                <InputLabel htmlFor="outlined-estatus">Estatus</InputLabel>
                                <Select
                                    labelId="outlined-estatus"
                                    id="estatus"
                                    label="Estatus"
                                    value={estatus}
                                    onChange={(e) => {setEstatus(e.target.value)}}
                                  >
                                    <MenuItem value={'En Proceso'}>En Proceso</MenuItem>
                                    <MenuItem value={'Aceptado'}>Aceptado</MenuItem>
                                    <MenuItem value={'Rechazado'}>Rechazado</MenuItem>
                                    <MenuItem value={'Pagado'}>Pagado</MenuItem>
                                    <MenuItem value={'Cancelado'}>Cancelado</MenuItem>

                                </Select>
                            </FormControl>
                    </div>

                    <div className="column">
                    </div>
                    <div className="column">
                    </div>
                    <div className="column">
                    </div>

                  </div>
                </div>
              ) : (
                <div className="row ">
                  <div className="column">
                  <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                          <KeyboardDatePicker
                          className="textField"
                              disableToolbar
                              variant="inline"
                              format="dd/MM/yyyy"
                              autoOk = 'true'
                              id="date-picker-inline"
                              inputVariant="outlined"
                              size="small"
                              label="Fecha de respuesta"
                              InputAdornmentProps={{ position: "start" }}
                              value={fechaRespuesta}
                              onChange={(value) => setFechaRespuesta(value) }
                          />
                      </MuiPickersUtilsProvider>
                  </div>

                  <div className="column">
                  <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                          <KeyboardDatePicker
                          className="textField"
                              disableToolbar
                              variant="inline"
                              format="dd/MM/yyyy"
                              autoOk = 'true'
                              id="date-picker-inline"
                              inputVariant="outlined"
                              size="small"
                              label="Fecha de resolución"
                              InputAdornmentProps={{ position: "start" }}
                              value={fechaResolucion}
                              onChange={(value) => setFechaResolucion(value) }
                          />
                      </MuiPickersUtilsProvider>
                  </div>

                  <div className="column">
                  <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                          <KeyboardDatePicker
                          className="textField"
                              disableToolbar
                              variant="inline"
                              format="dd/MM/yyyy"
                              autoOk = 'true'
                              id="date-picker-inline"
                              inputVariant="outlined"
                              size="small"
                              label="Fecha de solicitud de debito"
                              InputAdornmentProps={{ position: "start" }}
                              value={fechaSolicitud}
                              onChange={(value) => setFechaSolicitud(value) }
                          />
                      </MuiPickersUtilsProvider>
                  </div>

                  <div className="column">
                  <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                          <KeyboardDatePicker
                          className="textField"
                              disableToolbar
                              variant="inline"
                              format="dd/MM/yyyy"
                              autoOk = 'true'
                              id="date-picker-inline"
                              inputVariant="outlined"
                              size="small"
                              label="Fecha de aplicación de pago"
                              InputAdornmentProps={{ position: "start" }}
                              value={fechaAplicacion}
                              onChange={(value) => setFechaAplicacion(value) }
                          />
                      </MuiPickersUtilsProvider>
                  </div>

                  <div className="column">
                  <FormControl variant="outlined" className="textField" size="small">
                          <InputLabel htmlFor="outlined-estatus">Estatus</InputLabel>
                          <Select
                              labelId="outlined-estatus"
                              id="estatus"
                              label="Estatus"
                              value={estatus}
                              onChange={(e) => {setEstatus(e.target.value)}}
                            >
                              <MenuItem value={'En Proceso'}>En Proceso</MenuItem>
                              <MenuItem value={'Aceptado'}>Aceptado</MenuItem>
                              <MenuItem value={'Rechazado'}>Rechazado</MenuItem>
                              <MenuItem value={'Pagado'}>Pagado</MenuItem>
                              <MenuItem value={'Cancelado'}>Cancelado</MenuItem>

                          </Select>
                      </FormControl>
                    </div>
                  </div>

              )
          }



        <div className="row">
            <div className="column-max">
                <TextField id="Observaciones" label="Observaciones y sugerencias para agilizar la recuperación" className="textField" variant="outlined" size="small" multiline rows={4}
                value = {observaciones}
                onChange = { (e) => setObservaciones(e.target.value)}
                />
            </div>
        </div>

            <div className="row">
                    <div className="column-sm">
                    <Button variant="contained" color="secondary" className="button" onClick = {() => handleEditReporte() }>
                        Guardar
                    </Button>
                    </div>
            </div>


        </form>
          </div>
    )
}

export default withRouter(FormSiniestros)
