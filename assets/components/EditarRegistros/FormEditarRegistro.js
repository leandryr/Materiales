import React, { useState, useEffect } from 'react';
import './FormEditarRegistro.scss';

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
import { parseISO } from 'date-fns';
import Autocomplete from '@material-ui/lab/Autocomplete';
import InputAdornment from '@material-ui/core/InputAdornment';
import NumberFormat from 'react-number-format';
import PropTypes from 'prop-types';
import { withRouter } from 'react-router-dom';
import SvgIcon from "@material-ui/core/SvgIcon";
import  Regresar  from '../../img/back_button.js';




import { editRegistro  } from '../../api/api.js';

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


  

function FormSiniestros(props) {

    const {history , credentials, localidades, transportistas, rutas, plantas, areas, proveedores, registro,onEditRegistro, tipos, descripciones} = props;

    const [registro_id, setRegistroId] = useState(registro.id);
    const [localidad, setLocalidad] = useState(registro.localidad);
    const [planta, setPlanta] = useState(registro.planta);
    const [tipo, setTipo] = useState(registro.tipo);
    const [descripcion, setDescripcion] = useState(registro.descripcion);

    const [transportista, setTransportista] = useState(registro.transportista);
    const [referencia, setReferencia] = useState(registro.referencia);
    const [reclamadoUSD, setReclamadoUSD] = useState(registro.reclamadoUSD);
    const [reclamadoMXN, setReclamadoMXN] = useState(registro.reclamadoMXN);

    const [aceptado, setAceptado] = useState(registro.aceptado);
    const [recuperado, setRecuperado] = useState(registro.recuperado);
    const [ajustes, setAjustes] = useState(registro.ajustes);
    const [reclamoDocumentacion, setReclamoDocumentacion] = useState(registro.reclamoDocumentacion);

    const [reclamoProceso, setReclamoProceso] = useState(registro.reclamoProceso);
    const [ajuste, setAjuste] = useState(registro.ajuste);
    const [cancelado, setCancelado] = useState(registro.cancelado);
    const [flete, setFlete] = useState(registro.flete);

    const [menores, setMenores] = useState(registro.menores);
    const [excedente, setExcedente] = useState(registro.excedente);
    const [estimado, setEstimado] = useState(registro.estimado);

    const [fechaEvento, setFechaEvento] = useState(parseISO(registro.fechaEvento));
    const [fechaAsignacion, setFechaAsignacion] = useState(parseISO(registro.fechaAsignacion));
    const [fechaDocumentacion, setFechaDocumentacion] = useState(parseISO(registro.fechaDocumentacion));
    const [fechaEmision, setFechaEmision] = useState(parseISO(registro.fechaEmision));
    const [fechaRespuesta, setFechaRespuesta] = useState(parseISO(registro.fechaRespuesta));
  const [fechaAviso, setFechaAviso] = useState(parseISO(registro.fechaAviso));
    const [fechaAplicacion, setFechaAplicacion] = useState(parseISO(registro.fechaAplicacion));
    const [estatus, setEstatus] = useState(registro.estatus);

    const [tipoMaterial, setTipoMaterial] = useState(registro.tipoMaterial);
    const [escalado, setEscalado] = useState(registro.escalado);
    const [area, setArea] = useState(registro.area);
    const [fechaEscalacion, setFechaEscalacion] = useState(parseISO(registro.fechaEscalacion));
    const [fechaResolucion, setFechaResolucion] = useState(parseISO(registro.fechaResolucion));
    const [proveedor, setProveedor] = useState(registro.proveedor);
    const [ruta, setRuta] = useState(registro.ruta);
    const [caja, setCaja] = useState(registro.caja);
    const [comentarios, setComentarios] = useState(registro.comentarios);
    const [observaciones, setObservaciones] = useState(registro.observaciones);

    const [anoEvento, setAnoEvento] = useState(null);
    const [anoAsignacion, setAnoAsignacion] = useState(null);
    const [anoDocumentacion, setAnoDocumentacion] = useState(null);
    const [formaPago, setFormaPago] = useState(null);
    const [open, setOpen] = useState(false);



    const handleAutoCompleteLocalidad = (event, newValue) => {
      setLocalidad(newValue);
    }
    const handleAutoCompleteTransportista = (event, newValue) => {
      setTransportista(newValue);

    }

    const handleAutoCompleteRuta = (event, newValue) => {
      setRuta(newValue);
    }

      const handleAutoCompleteArea = (event, newValue) => {
        setArea(newValue);
      }

      const handleAutoComplete = (event, newValue) => {

        handleAutoCompleteProveedor(newValue);
    
      }
    
      const handleAutoCompleteProveedor = (event, newValue) => {
        setProveedor(newValue);
    }
    const handleAutoCompletePlanta = (event, newValue) => {
      setPlanta(newValue);

}

const handleAutoCompleteTipo= (event, newValue) => {
  setTipo(newValue);

}

const handleAutoCompleteDescripcion = (event, newValue) => {
  setDescripcion(newValue);

}

    const handleEditRegistro = () => {

      const data = {
        id: registro_id,
        localidad: localidad,
        planta: planta,
        tipo: tipo,
        descripcion: descripcion,
        transportista: transportista,
        referencia: referencia,
        reclamadoUSD: reclamadoUSD,
        reclamadoMXN: reclamadoMXN,
        aceptado: aceptado,
        recuperado: recuperado,
        ajustes: ajustes,
        reclamoDocumentacion: reclamoDocumentacion,
        reclamoProceso: reclamoProceso,
        ajuste: ajuste,
        cancelado: cancelado,
        flete: flete,
        menores: menores,
        excedente: excedente,
        estimado: estimado,
        fechaEvento: fechaEvento,
        fechaAsignacion: fechaAsignacion,
        fechaDocumentacion: fechaDocumentacion,
        fechaEmision: fechaEmision,
        fechaRespuesta: fechaRespuesta,
        fechaAviso: fechaAviso,
        fechaAplicacion: fechaAplicacion,
        estatus: estatus,
        tipoMaterial: tipoMaterial,
        escalado: escalado,
        area: area,
        fechaEscalacion: fechaEscalacion,
        fechaResolucion: fechaResolucion,
        proveedor:proveedor,
        ruta: ruta,
        caja: caja,
        comentarios: comentarios,
        observaciones: observaciones,
        anoEvento: anoEvento,
        anoAsignacion: anoAsignacion,
        anoDocumentacion: anoDocumentacion,
        formaPago: formaPago,
        
      }

      editRegistro(credentials,data)
      .then((datos) => {
        onEditRegistro(datos.validation.item);
      })

      .catch((e) => {
        console.log(e);

      });
    }

    const handleListItemClick = (url) => {
        history.push('/home/registro');
    };




    return (
      <div className="editar_registro">

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

                    <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-estatus">Planta</InputLabel>
                        <Select
                            labelId="outlined-estatus"
                            id="planta"
                            label="Planta"
                            value={plantas}
                            onChange={(e) => {setPlanta(e.target.value)}}
                          >
                          {(!!plantas) ? plantas.map((plant) => (   <MenuItem value={plant.planta}>{plant.planta}</MenuItem>  ) ): ('')}
                        </Select>
                    </FormControl>

                    </div>

                    <div className="column">
                    <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-estatus">Tipo de Evento</InputLabel>
                        <Select
                            labelId="outlined-estatus"
                            id="tipoevento"
                            label="Tipo de Evento"
                            value={tipo}
                            onChange={(e) => {setTipo(e.target.value)}}
                          >
                          {(!!tipos) ? tipos.map((ty) => (   <MenuItem value={ty.tipo}>{ty.tipo}</MenuItem>  ) ): ('')}
                        </Select>
                    </FormControl>


                    </div>

                    <div className="column">

                    <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-estatus">Descripci&oacute;n del evento</InputLabel>
                        <Select
                            labelId="outlined-estatus"
                            id="descripcionevento"
                            label="Descripci&oacute;n del evento"
                            value={descripcion}
                            onChange={(e) => {setDescripcion(e.target.value)}}
                          >
                          {(!!descripciones) ? descripciones.map((desc) => (   <MenuItem value={desc.descripcion}>{desc.descripcion}</MenuItem>  ) ): ('')}
                        </Select>
                    </FormControl>

                    </div>
            </div>

            <div className="row">
                <div className="column">
                <div className="column">
                <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-estatus">Transportista/Responsable</InputLabel>
                        <Select
                            labelId="outlined-estatus"
                            id="transportistaAutocomplete"
                            label="Transportista/Responsable"
                            value={transportista}
                            onChange={(e) => {setTransportista(e.target.value)}}
                          >
                          {(!!transportistas) ? transportistas.map((transpor) => (   <MenuItem value={transpor.transportista}>{transpor.transportista}</MenuItem>  ) ): ('')}
                        </Select>
                    </FormControl>
                    </div>
                    <div className="column">
                        <TextField id="Referencia" label="Referenc&iacute;a" className="textField" variant="outlined" size="small"
                        value={referencia}
                        onChange={(e) => {setReferencia(e.target.value);}}
                        />
                    </div>
                </div>
                <div className="column">
                    <div className="column">
                        <TextField id="MontoReclamadoUSD" label="Monto reclamado USD" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={reclamadoUSD}
                        onChange={(e) => {setReclamadoUSD(e.target.value);}}
                         />
                    </div>
                    <div className="column">
                        <TextField id="MontoReclamadoMXN" label="Monto reclamado MXN" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={reclamadoMXN}
                        onChange={(e) => {setReclamadoMXN(e.target.value);}}
                        />
                    </div>
                </div>
            </div>

            <div className="row">
            <div className="column">
                    <div className="column">
                        <TextField id="MontoAceptado" label="Monto aceptado" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={aceptado}
                        onChange={(e) => {setAceptado(e.target.value);}}
                        />
                    </div>
                    <div className="column">
                        <TextField id="MontoRecuperado" label="Monto recuperado" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={recuperado}
                        onChange={(e) => {setRecuperado(e.target.value);}}
                        />
                    </div>
                </div>
                <div className="column">
                    <div className="column">
                        <TextField id="AjustesMGO" label="Ajustes MGO" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={ajustes}
                        onChange={(e) => {setAjustes(e.target.value);}}
                        />
                    </div>
                    <div className="column">
                        <TextField id="ReclamoEnDoc" label="Reclamo en documentaci&oacute;n" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={reclamoDocumentacion}
                        onChange={(e) => {setReclamoDocumentacion(e.target.value);}}  />
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="column">
                    <div className="column">
                        <TextField id="ReclamoEnProceso" label="Reclamo en proceso" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={reclamoProceso}
                        onChange={(e) => {setReclamoProceso(e.target.value);}} />
                    </div>
                    <div className="column">
                        <TextField id="AjuesteReversion" label="Ajuste / Reversión de partidas" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={ajuste}
                        onChange={(e) => {setAjuste(e.target.value);}} />
                    </div>
                </div>
                <div className="column">
                    <div className="column">
                        <TextField id="Cancelado" label="Cancelado" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={cancelado}
                        onChange={(e) => {setCancelado(e.target.value);}}  />
                    </div>
                    <div className="column">
                        <TextField id="Flete" label="Flete del BI30 no incluido en reclamo" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={flete}
                        onChange={(e) => {setFlete(e.target.value);}}
                        />
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="column">
                    <div className="column">
                        <TextField id="MenoresUSD" label="Menores de USD $500" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={menores}
                        onChange={(e) => {setMenores(e.target.value);}} />
                    </div>
                    <div className="column">
                        <TextField id="ExcedenteContrato" label="Excedente de contrato" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={excedente}
                        onChange={(e) => {setExcedente(e.target.value);}} />
                    </div>
                </div>
                <div className="column">
                    <div className="column">
                        <TextField id="MontoRecuperar" label="Monto estimado a recuperar" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: NumberFormatCustom,
                        }}
                        value={estimado}
                        onChange={(e) => {setEstimado(e.target.value);}}
                        />
                    </div>
                    <div className="column">
                    </div>
                </div>
            </div>

            <div className="row">
                <div className="column">
                    <div className="column">
                        <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale} >
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Fecha del evento"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaEvento}
                                onChange={(value) => {setFechaEvento(value)}}
                            />
                        </MuiPickersUtilsProvider>
                    </div>
                    <div className="column">
                        <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Fecha de asignación"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaAsignacion}
                                onChange={(value) => {setFechaAsignacion(value)}}
                            />
                        </MuiPickersUtilsProvider>
                    </div>
                    <div className="column">
                        <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Fecha de documentación"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaDocumentacion}
                                onChange={(value) => {setFechaDocumentacion(value)}}
                            />
                        </MuiPickersUtilsProvider>
                    </div>
                </div>
                <div className="column">
                    <div className="column">
                        <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Fecha de emisión"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaEmision}
                                onChange={(value) => {setFechaEmision(value)}}
                            />
                        </MuiPickersUtilsProvider>
                    </div>
                    <div className="column">
                        <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Fecha de respuesta"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaRespuesta}
                                onChange={(value) => {setFechaRespuesta(value)}}
                            />
                        </MuiPickersUtilsProvider>
                    </div>
                    <div className="column">
                        <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Aviso de pago"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaAviso}
                                onChange={(value) => {setFechaAviso(value)}}

                            />
                        </MuiPickersUtilsProvider>
                    </div>
                </div>
            </div>
            <div className="row">
                    <div className="column">
                        <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Fecha de aplicación"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaAplicacion}
                                onChange={(value) => {setFechaAplicacion(value)}}
                            />
                        </MuiPickersUtilsProvider>
                    </div>
                    
                    <div className="column">
                        <TextField id="anoEvento" label="Año de Evento" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: YearFormatCustom,
                        }}
                        value={anoEvento}
                        onChange={(e) => {setAnoEvento(e.target.value);}} />
                    </div>
                    <div className="column">
                        <TextField id="anoAsignacion" label="Año de Asignacion" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: YearFormatCustom,
                        }}
                        value={anoAsignacion}
                        onChange={(e) => {setAnoAsignacion(e.target.value);}} />
                    </div>
                    <div className="column">
                        <TextField id="anoDocumentacion" label="Año de Documentacion" className="textField" variant="outlined" size="small"
                        InputProps={{
                          inputComponent: YearFormatCustom,
                        }}
                        value={anoDocumentacion}
                        onChange={(e) => {setAnoDocumentacion(e.target.value);}} />
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

            </div>
            <div className="row">
                <div className="column">
                    <TextField id="TipoMaterial" label="Tipo de material" className="textField" variant="outlined" size="small"
                    value={tipoMaterial}
                    onChange={(e) => {setTipoMaterial(e.target.value);}}
                    />
                </div>
                <div className="column">

                <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-estatus">Escalado</InputLabel>
                        <Select
                            labelId="outlined-estatus"
                          id="escalado"
                          label="Escalado"
                            value={escalado}
                            onChange={(e) => {setEscalado(e.target.value)}}
                          >
                            <MenuItem value={'Si'}>Si</MenuItem>
                            <MenuItem value={'No'}>No</MenuItem>

                        </Select>
                    </FormControl>


                </div>
                <div className="column">

                <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-estatus">Area</InputLabel>
                        <Select
                            labelId="outlined-estatus"
                            id="area"
                            label="Area"
                            value={area}
                            onChange={(e) => {setArea(e.target.value)}}
                          >
                          {(!!areas) ? areas.map((ar) => (   <MenuItem value={ar.area}>{ar.area}</MenuItem>  ) ): ('')}
                        </Select>
                    </FormControl>


                </div>
                <div className="column">
                <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Fecha de escalación"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaEscalacion}
                                onChange={(value) => {setFechaEscalacion(value)}}
                            />
                        </MuiPickersUtilsProvider>
                </div>
                <div className="column">
                <MuiPickersUtilsProvider utils={DateFnsUtils} locale={esLocale}>
                            <KeyboardDatePicker
                                disableToolbar
                                variant="inline"
                                format="dd-MM-yyyy"
                                id="date-picker-inline"
                                inputVariant="outlined"
                                size="small"
                                label="Fecha de resolución"
                                InputAdornmentProps={{ position: "start" }}
                                value={fechaResolucion}
                                onChange={(value) => {setFechaResolucion(value)}}
                            />
                        </MuiPickersUtilsProvider>
                </div>
            </div>
            <div className="row">
                <div className="column">
                <Autocomplete
                    open={open}
                    onInputChange={(_, value) => {
                      if (value.length === 0) {
                        if (open) setOpen(false);
                      } else {
                        if (!open) setOpen(true);
                      }
                    }}
                    onClose={() => setOpen(false)}
                    id="provedorField"
                    freeSolo
                    className="textField"
                    onChange={handleAutoComplete}
                    options={proveedores.map((prov) => prov.proveedor)}
                    renderInput={(params) => (
                      <TextField  {...params} id="proveedor" label="Proveedor" className="textField" variant="outlined" size="small"
                      value={proveedor}
                      onChange={(e) => {handleAutoCompleteProveedor(e.target.value)}} />
                    )}
                  />

                </div>

                <div className="column">

                <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-estatus">Ruta</InputLabel>
                        <Select
                            labelId="outlined-estatus"
                            id="Ruta"
                            label="Ruta"
                            value={ruta}
                            onChange={(e) => {setRuta(e.target.value)}}
                          >
                          {(!!rutas) ? rutas.map((rut) => (   <MenuItem value={rut.ruta}>{rut.ruta}</MenuItem>  ) ): ('')}
                        </Select>
                    </FormControl>


                </div>
                <div className="column">
                    <TextField id="Caja" label="Caja" className="textField" variant="outlined" size="small"
                    value={caja}
                    onChange={(e) => {setCaja(e.target.value);}}
                    />
                </div>
                
                <div className="column">
                <FormControl variant="outlined" className="textField" size="small">
                            <InputLabel htmlFor="outlined-formaPago">Forma de pago</InputLabel>
                            <Select
                                labelId="outlined-formaPago"
                                id="formaPago"
                                label="Forma de Pago"
                                value={formaPago}
                                onChange={(e) => {setFormaPago(e.target.value)}}
                              >
                                <MenuItem value={'Transferencia'}>Transferencia</MenuItem>
                                <MenuItem value={'Debito'}>Débito</MenuItem>
                                <MenuItem value={'Cheque'}>Cheque</MenuItem>
                                <MenuItem value={'Deposito'}>Depósito</MenuItem>
                                <MenuItem value={'POBox'}>Pobox</MenuItem>

                            </Select>
                        </FormControl>

                    </div>

                    <div className="column">

</div>
            </div>
            <div className="row">
                <div className="column-max">
                    <TextField id="Comentarios" label="Comentarios" className="textField" variant="outlined" size="small" multiline rows={4}
                    value={comentarios}
                    onChange={(e) => {setComentarios(e.target.value);}}
                    />
                </div>
            </div>
            <div className="row">
                <div className="column-max">
                    <TextField id="Observaciones" label="Observaciones por diferencias y cancelaciones" className="textField" variant="outlined" size="small" multiline rows={4}
                    value={observaciones}
                    onChange={(e) => {setObservaciones(e.target.value);}}
                    />
                </div>
            </div>
            <div className="row">
                <div className="column">
                    <div className="column">

                    </div>
                    <div className="column">
                       
                    </div>
                    <div className="column">
                        
                    </div>
                </div>
                <div className="column">
                    <div className="column">
                        
                    </div>
                    <div className="column">
                    <Button variant="contained" color="secondary" className="button"
                    onClick = {() => handleEditRegistro() }
                    >
                        Guardar
                    </Button>
                    </div>
                </div>
            </div>


        </form>

      </div>
    )
}

export default withRouter(FormSiniestros)
