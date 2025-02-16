import React , {useEffect} from 'react';
import SvgIcon from "@material-ui/core/SvgIcon";
import { withRouter } from 'react-router-dom';
import './Reporte.scss';
import  Close  from '../../img/close.js';
import  Excel  from '../../img/excel.js';
import  Regresar  from '../../img/back_button.js';
import  Edit  from '../../img/edit.js';
import  Calendario  from '../../img/calendario.js';
import InputAdornment from '@material-ui/core/InputAdornment';
import NumberFormat from 'react-number-format';
import PropTypes from 'prop-types';
import {
    TextField, Select, InputLabel,
    FormControl, MenuItem, Button
} from '@material-ui/core';
import {getRegistro, getExcelReporte , deleteFile, documentarReporte} from '../../api/api.js';

function numberWithCommas(x) {
  if(!!x){
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

  }
  return '0.00';

}


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

function Reporte(props) {
    const { history, reporte, credentials, onClickRegistro } = props;


    const openFile = (enlace) => {
      let host = window.location.host;
      const newWindow = window.open('http://' + host + '/build/'+enlace , '_blank', 'noopener,noreferrer')
      if (newWindow){
        newWindow.opener = null
      }
    }

    const handleDescargaReporte = () => {

      let enlace = '';
      getExcelReporte(credentials, reporte.id)
      .then((data) => {
        openFile(data.validation.enlace);
        enlace = data.validation.enlace;
      })
      .then((data) => {
        deleteFile(credentials, enlace);
      })
      .catch((e) =>{

      })

    }

    const irRegistro = (id) => {

      getRegistro(credentials,id)
      .then((datos) => {
        onClickRegistro(datos.item);
      })

    }
    const goBack = () => {
      history.push('/home/reportes');
    }

    const ponerFecha = (fecha) => {
      let fechaString = '';

      if(!!fecha){

        //fechaString = fecha.toLocaleDateString('es-MX');
        fechaString = fecha;
      }

      return fechaString;
    }


    return (
        <div className="reporte">
            <div className="content-icon-header" >
              <div onClick = {()=> goBack()}>
                  <span >
                  <SvgIcon component={Regresar} viewBox="0 0 30 30" />
                   Regresar
                  </span>
                </div>



                <div style={{ display: 'flex', width: '100%', justifyContent: 'flex-end', alignItems: 'center' }}>

                <Button variant="contained" color="secondary" className="button"
                style={{ marginRight: '5.625rem' }}
                onClick={() => irRegistro(reporte.id)}>
                    Registro Completo
                </Button>

                <Button variant="contained" style={{ backgroundColor: '#4CAF50', color: '#FFFFFF', marginRight: '5.625rem' }}
                onClick = {()=> handleDescargaReporte()}>
                    <SvgIcon component={Excel} />
                    <span className="space"></span>
                    Descargar Excel
                </Button>

                </div>
            </div>

            <div className="row border">
                <div className="column">
                    <div className="name">Ultima actualización:
                    <span className="date"> {ponerFecha(reporte.actualizacion)}</span>
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="column direction">
                    <div className="miniTitle">Localidad:</div>
                    <div className="miniValue">{reporte.localidad}</div>
                </div>
                <div className="column direction">
                    <div className="miniTitle">Ref. Claim:</div>
                    <div className="miniValue">{reporte.claim}</div>
                </div>
                <div className="column direction">
                    <div className="miniTitle">Transportista/Responsable:</div>
                    <div className="miniValue">{reporte.transportista}</div>
                </div>
                <div className="column direction">
                    <div className="miniTitle">Tipo de daño:</div>
                    <div className="miniValue">{reporte.tipo}</div>
                </div>
            </div>
            <div className="row">
                <div className="column direction">
                <div className="miniTitle">Monto reclamado USD:</div>
                <div className="miniValue">$ {numberWithCommas(reporte.reclamadoUSD)}</div>

                </div>
                <div className="column direction">
                <div className="miniTitle">Monto reclamado MXN:</div>
                <div className="miniValue">$ {numberWithCommas(reporte.reclamadoMXN)}</div>


                </div>
                <div className="column direction">
                <div className="miniTitle">Monto excedente de contrato MXN:</div>
                <div className="miniValue">$ {numberWithCommas(reporte.excedente)}</div>


                </div>
                <div className="column direction">
                <div className="miniTitle">Monto estimado de recuperación MXN:</div>
                <div className="miniValue">$ {numberWithCommas(reporte.estimado)}</div>


                </div>
            </div>
            <div className="row">
                <div className="column direction">
                <div className="miniTitle">Monto rechazado MXN:</div>
                <div className="miniValue">$ {numberWithCommas(reporte.rechazado)}</div>



                </div>
                <div className="column direction">
                <div className="miniTitle">Monto aceptado MXN:</div>
                <div className="miniValue">$ {numberWithCommas(reporte.aceptado)}</div>


                </div>
                <div className="column direction">
                <div className="miniTitle">Monto cancelado MXN:</div>
                <div className="miniValue">$ {numberWithCommas(reporte.cancelado)}</div>


                </div>
                <div className="column direction">
                <div className="miniTitle">Flete no incluido en reclamo:</div>
                <div className="miniValue">$ {numberWithCommas(reporte.flete)}</div>


                </div>
            </div>

            {reporte.mayor45 ? (
              <div>
              <div className="row">
                  <div className="column direction">
                      <div className="miniTitle">Fecha del evento:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaEvento)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de emisión:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaEmision)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Días del evento a la emisión:</div>
                      <div className="miniValue">{reporte.eventoEmision}</div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Días de la emisión a la fecha:</div>
                      <div className="miniValue">{reporte.emisionHoy}</div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de 1er notificación a RM:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fecha1)}
                          </span>
                      </div>
                  </div>
              </div>
              <div className="row">
                  <div className="column direction">
                      <div className="miniTitle">Fecha de 2da notificación a RM:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fecha2)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de 3ra notificación a RM:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fecha3)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de escalación:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaEscalacion)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Área de escalación/ Responsable:</div>
                      <div className="miniValue">{reporte.area}</div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de resolución:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaResolucion)}
                          </span>
                      </div>
                  </div>
              </div>
              <div className="row">
                  <div className="column direction">
                      <div className="miniTitle">Días desde la escalación a la resolución:</div>
                      <div className="miniValue">{reporte.escalacionResolucion}</div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Estatus:</div>
                      <div className="miniValue content-icon"><span>

                          {reporte.estatus}
                          </span></div>
                  </div>
                  <div className="column direction">
                  </div>
                  <div className="column direction">
                  </div>
                  <div className="column direction">
                  </div>
              </div>
              <div className="row">
                  <div className="column direction">
                      <div className="miniTitle">Observaciones y sugerencias para agilizar la recuperación</div>
                      <div className="miniValue">{reporte.observaciones}</div>
                  </div>
              </div>
              </div>
            ) : (

              <div>
              <div className="row">
                  <div className="column direction">
                      <div className="miniTitle">Fecha del evento:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaEvento)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de emisión:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaEmision)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Días del evento a la emisión:</div>
                      <div className="miniValue">{reporte.eventoEmision}</div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Días de la emisión a la fecha:</div>
                      <div className="miniValue">{reporte.emisionHoy}</div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de Respuesta:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaRespuesta)}
                          </span>
                      </div>
                  </div>
              </div>
              <div className="row">
                  <div className="column direction">
                      <div className="miniTitle">Fecha de Resolución:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaResolucion)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de solicitud de debito:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaSolicitud)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Fecha de aplicación de pago:</div>
                      <div className="miniValue content-icon">
                          <span>
                              <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                              {ponerFecha(reporte.fechaAplicacion)}
                          </span>
                      </div>
                  </div>
                  <div className="column direction">
                      <div className="miniTitle">Estatus:</div>
                      <div className="miniValue content-icon"><span>

                          {reporte.estatus}
                          </span></div>
                  </div>

              </div>

              <div className="row">
                  <div className="column direction">
                      <div className="miniTitle">Observaciones y sugerencias para agilizar la recuperación</div>
                      <div className="miniValue">{reporte.observaciones}</div>
                  </div>
              </div>
              </div>

            ) }


        </div>
    )
}

export default withRouter(Reporte);
