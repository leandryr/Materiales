import React, { useState } from 'react';
import './FormEditarDocumentado.scss';
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




import { editDocumentado  } from '../../api/api.js';

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
  const {history, credentials, localidades,doc, plantas, onEdit} = props;

  const [reporte_id, setReporteId] = useState(doc.idD);
    const [localidad, setLocalidad] = useState(doc.localidadD);
    const [claim, setClaim] = useState(doc.claimD);
    const [codigo, setCodigo] = useState(doc.codigo);
    const [planta, setPlanta] = useState(doc.plantaD);
    const [numero, setNumero] = useState(doc.numero);
    const [cantidad, setCantidad] = useState(doc.cantidad);
    const [perdidaConFlete, setPerdidaConFlete] = useState(doc.perdidaConFlete);
    const [perdidaSinFlete, setPerdidaSinFlete] = useState(doc.perdidaSinFlete);
    const [fechaNotificacion, setFechaNotificaion] = useState(doc.fechaNotificacion);
    const [documentacionFaltante, setDocumentacionFaltante] = useState(doc.documentacionFaltante);
    const [area, setArea] = useState(doc.areaD);
    const [estatus, setEstatus] = useState(doc.estatusD);


    const handleAutoCompleteLocalidad = (event, newValue) => {
      setLocalidadField(newValue);
    }

    const handleAutoCompleteTransportista = (event, newValue) => {
      setTransportista(newValue);

    }



  const reset = () => {
  }

    const handleEditDocumentado = () => {
            const data = {
              id : reporte_id,
              localidad: localidad,
              claim: claim,
              codigo: codigo,
              planta: planta,
              numero: numero,
              cantidad: cantidad,
              fechaNotificacion: fechaNotificacion,
              perdidaSinFlete: perdidaSinFlete,
              perdidaConFlete: perdidaConFlete,
              documentacionFaltante: documentacionFaltante,
              area: area,
              estatus: estatus,
            };


      editDocumentado(credentials,data)
      .then((datos) => {
        onEdit(reporte_id);
      })
      .catch((e) =>{

      });
    }


    const handleNewReporte = () => {

    }


    const handleListItemClick = () => {
        history.push('/home/documentado');
    };

    return (
      <div className="editar_reporte">

      <div className="content-icon-header" >

          <div onClick={() => handleListItemClick()}>
              <span >
              <SvgIcon component={Regresar} viewBox="0 0 30 30" />
               Regresar
              </span>
          </div>
      </div>


        <form noValidate autoComplete="off" className="formSiniestros" >
            <div className="row">

            <div className="column">
                <TextField id="Referencia" label="Referenc&iacute;a Claim" className="textField" variant="outlined" size="small"
                value={claim}
                onChange={(e) => {setClaim(e.target.value);}}
                />
            </div>

              <div className="column" >
              <FormControl variant="outlined" className="textField" size="small">
                  <InputLabel htmlFor="outlined-estatus">Localidad</InputLabel>
                  <Select
                      labelId="outlined-estatus"
                      id="localidad"
                      label="Localidad"
                      value={localidad}
                      onChange={(e) => {setLocalidad(e.target.value)}}
                    >
                    {(!!localidades) ? localidades.map((locali) => (   <MenuItem value={locali.localidad}>{locali.localidad}</MenuItem>  ) ): ('')}
                  </Select>
              </FormControl>

              </div>

              <div className="column">
                  <TextField id="codigo" label="Código de daño" className="textField" variant="outlined" size="small"
                  value={codigo}
                  onChange={(e) => {setCodigo(e.target.value);}}
                  />
              </div>

              <div className="column">

              <FormControl variant="outlined" className="textField" size="small">
                  <InputLabel htmlFor="outlined-estatus">Planta</InputLabel>
                  <Select
                      labelId="outlined-estatus"
                      id="planta"
                      label="Planta"
                      value={planta}
                      onChange={(e) => {setPlanta(e.target.value)}}
                    >
                    {(!!plantas) ? plantas.map((plant) => (   <MenuItem value={plant.planta}>{plant.planta}</MenuItem>  ) ): ('')}
                  </Select>
              </FormControl>

              </div>


            </div>
            <div className="row">
              <div className="column">
                <TextField id="numero" label="Número de parte" className="textField" variant="outlined" size="small"
                value = {numero}
                onChange = { (e) => {setNumero(e.target.value);}}
                />
              </div>
              <div className="column">
                <TextField id="cantidad" label="Cantidad de piezas" className="textField" variant="outlined" size="small"
                value = {cantidad}
                onChange = { (e) => {setCantidad(e.target.value);}}
                />
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
                          label="Fecha de notificación RV"
                          InputAdornmentProps={{ position: "start" }}
                          value={fechaNotificacion}
                          onChange={(value) => setFechaNotificaion(value)}
                      />
                  </MuiPickersUtilsProvider>
              </div>

              <div className="column">

                  <TextField id="valorPerdidaSinFlete" label="Valor de perdida sin flete" className="textField" variant="outlined" size="small"
                  value = {perdidaSinFlete}
                  onChange = { (e) => {setPerdidaSinFlete(e.target.value);}}
                  InputProps={{
                    inputComponent: NumberFormatCustom,
                  }}
                  />
              </div>


            </div>

            <div className="row">

            <div className="column">
              <TextField id="valorPerdidaConFlete" label="Valor de perdida con flete" className="textField" variant="outlined" size="small"
              value = {perdidaConFlete}
              onChange = { (e) => {setPerdidaConFlete(e.target.value);}}
              InputProps={{
                inputComponent: NumberFormatCustom,
              }}
              />
            </div>



            <div className="column">
              <TextField id="area_responsables" label="Área y Responsables" className="textField" variant="outlined" size="small"
              value = {area}
              onChange = { (e) => setArea(e.target.value)}
               />
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
                  <TextField id="documentacion_faltante" label="Documentación faltante" className="textField" variant="outlined" size="small" multiline rows={1}
                  value = {documentacionFaltante}
                  onChange = { (e) => setDocumentacionFaltante(e.target.value)}
                  />
              </div>

            </div>


            <div className="row">
                    <div className="column-sm">
                    <Button variant="contained" color="secondary" className="button" onClick = {() => handleEditDocumentado() }>
                        Guardar
                    </Button>
                    </div>
            </div>
        </form>

          </div>
    )
}

export default withRouter(FormSiniestros)
