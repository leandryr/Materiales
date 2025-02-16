import React from 'react'
import SvgIcon from "@material-ui/core/SvgIcon";
import Button from '@material-ui/core/Button';
import { withRouter } from 'react-router-dom';
import './Registro.scss'

import  Close  from '../../img/close.js';
import  Checked  from '../../img/checked.js';

import  Excel  from '../../img/excel.js';
import  Regresar  from '../../img/back_button.js';
import  Edit  from '../../img/edit.js';
import Calendario  from '../../img/calendario.js';


function numberWithCommas(x) {
  if(!!x){
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }
    return '0.00';
}

function Registro(props) {
    const { history, credentials, registro, onDescargarExcerRegistro, onClickEdit } = props;
    const defaultPath = '/home/';
    const handleListItemClick = (url) => {
        history.push(`${defaultPath}${url}`);
    };


    const handleEditRegistro = () => {
      onClickEdit();
    }


    return (
      <div className="registro">
          <div className="content-icon-header" >
          <div onClick={() => handleListItemClick('historial')}>
              <span >
              <SvgIcon component={Regresar} viewBox="0 0 30 30" />
               Regresar
              </span>
          </div>
          <div style={{ display: 'flex', width: '100%', justifyContent: 'flex-end', alignItems: 'center' }}>

                  <Button variant="contained" style={{ backgroundColor: '#4CAF50', color: '#FFFFFF', marginRight: '5.625rem' }}
                  onClick = {(e) => onDescargarExcerRegistro()}
                   >
                      <SvgIcon component={Excel} />
                      <span className="space"></span>
                  Descargar Excel
                  </Button>

                  {(credentials.rol === 'ROLE_ADMINISTRADOR' || credentials.rol === 'ROLE_CAPTURISTA') ? (
                    <div onClick = {()=> handleEditRegistro()}>
                    <SvgIcon component={Edit} viewBox="0 0 25 25" />
                    </div>
                                ):
                                ('')}
              </div>
          </div>

          <div className="row border">
              <div className="column">
                  <div className="name">Ultima actualización:
              <span className="date"> {registro.fechaActualizacion}</span>
                  </div>
              </div>
          </div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Localidad:</div>
                  <div className="miniValue">{registro.localidad}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Planta:</div>
                  <div className="miniValue">{registro.planta}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Tipo de evento:</div>
                  <div className="miniValue">{registro.tipo}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Descripción del evento:</div>
                  <div className="miniValue">{registro.descripcion}</div>
              </div>
          </div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Transportista:</div>
                  <div className="miniValue">{registro.transportista}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Referencía:</div>
                  <div className="miniValue">{registro.referencia}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Monto reclamado USD:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.reclamadoUSD)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Monto reclamado MXN:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.reclamadoMXN)}</div>
              </div>
          </div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Monto aceptado:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.aceptado)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Monto recuperado:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.recuperado)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Ajustes MGO:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.ajustes)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Reclamo en documentación:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.reclamoDocumentacion)}</div>
              </div>
          </div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Reclamo en proceso:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.reclamoProceso)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Ajuste / Reversión de partidas:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.ajuste)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Cancelado:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.cancelado)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Flete del BI30 no incluido en reclamo:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.flete)}</div>
              </div>
          </div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Menores de USD $500:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.menores)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Excedente de contrato:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.excedente)}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Monto estimado a recuperar:</div>
                  <div className="miniValue">$ {numberWithCommas(registro.estimado)}</div>
              </div>
              <div className="column direction">
              </div>
          </div>
          <div className="row"></div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Fecha del evento:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaEvento}
                  </span>
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Fecha de asignación:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaAsignacion}
                  </span>
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Días de evento a notificación:</div>
                  <div className="miniValue">{registro.eventoNotificacion}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Fecha de documentación:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaDocumentacion}
                  </span>
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Días de notificación a doc...:</div>
                  <div className="miniValue">{registro.notificacionDocumentacion}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Fecha de emisión RV:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaEmision}
                  </span>
                  </div>
              </div>
          </div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Días de documentación a emisión:</div>
                  <div className="miniValue">{registro.documentacionEmision}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Fecha de respuesta carrier:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaRespuesta}
                  </span>
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Días de emisión a respuesta:</div>
                  <div className="miniValue">{registro.emisionRespuesta}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Fecha aviso de pago:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaAviso}
                  </span>
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Fecha de aplicación contable:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaAplicacion}
                  </span>
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Días de atraso desde emisión:</div>
                  <div className="miniValue">{registro.diasTotales}</div>
              </div>
          </div>
          <div className="row">
              
              <div className="column direction">
                <div className="miniTitle">
                    Año de Evento:
                </div>
                <div className="miniValue content-icon">
                <span>
                    {registro.anoEvento}
                </span>
                </div>
              </div>

              <div className="column direction">
                    <div className="miniTitle">
                        Año de Asignacion:
                    </div>
                    <div className="miniValue content-icon">
                        <span>
                        {registro.anoAsignacion}
                        </span>
                    </div>
                </div>

              <div className="column direction">
                <div className="miniTitle">Año de documentacion:</div>
                <div className="miniValue content-icon">
                    <span>
                        {registro.anoDocumentacion}
                    </span>
                </div>
              </div>

              <div className="column direction">
                  <div className="miniTitle">Estatus:</div>
                  <div className="miniValue content-icon">
                    <span>
                        {registro.estatus}
                    </span>
                  </div>
              </div>

              <div className="column direction">
              </div>
              <div className="column direction">
              </div>
          </div>
          <div className="row"></div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Tipo de material:</div>
                  <div className="miniValue">{registro.tipoMaterial}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Escalado:</div>
                  <div className="miniValue">
                      {registro.escalado}
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Área:</div>
                  <div className="miniValue">
                    {registro.area}
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Fecha de escalación:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaEscalacion}
                  </span>
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Fecha de resolución:</div>
                  <div className="miniValue content-icon">
                      <span>
                          <SvgIcon component={Calendario} viewBox="0 0 22 22" />
                      {registro.fechaResolucion}
                  </span>
                  </div>
              </div>
          </div>
          <div className="row">
              <div className="column direction">
                  <div className="miniTitle">Proveedor:</div>
                  <div className="miniValue">{registro.proveedor}</div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Ruta:</div>
                  <div className="miniValue">
                      {registro.ruta}
                  </div>
              </div>
              <div className="column direction">
                  <div className="miniTitle">Caja:</div>
                  <div className="miniValue">
                      {registro.caja}
                  </div>
              </div>

              <div className="column direction">

                <div className="miniTitle">Forma de pago:</div>
                  <div className="miniValue">
                      {registro.formaPago}
                  </div>
             </div>


              <div className="column direction">
              </div>

          </div>
          <div className="row">
              <div className="column-lg direction">
                  <div className="miniTitle">Comentarios</div>
                  <div className="miniValue">{registro.comentarios}</div>
              </div>
          </div>
          <div className="row">
              <div className="column-lg direction">
                  <div className="miniTitle">Observaciones por diferencias y cancelaciones</div>
                  <div className="miniValue">{registro.observaciones}</div>
              </div>
          </div>
          <div className="row"></div>
          <div className="row">
              <div className="column direction">
                
              </div>
              <div className="column direction">
                  
              </div>
              <div className="column direction">
                  
              </div>
              <div className="column direction">
                  
              </div>
              <div className="column direction">
              </div>
              <div className="column direction">
              </div>
          </div>
      </div>
    )
}

export default withRouter(Registro);
