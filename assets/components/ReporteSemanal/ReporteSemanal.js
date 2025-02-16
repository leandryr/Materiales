import React from 'react';
import './ReporteSemanal.scss';
import SvgIcon from "@material-ui/core/SvgIcon";
import  Back  from '../../img/back.js';
import  Checked  from '../../img/checked.js';
import  Excel  from '../../img/excel.js';
import  Close  from '../../img/close.js';
import  Alert  from '../../img/alert.js';
import  Rejected  from '../../img/rejected.js';
import Button from '@material-ui/core/Button';
import { withRouter } from 'react-router-dom';
import { getExcelSemanal , deleteFile} from '../../api/api.js';


function ReporteSemanal(props) {
    const {  history, semana, credentials, onClickReporte, estatus, tipoReporte} = props;
    const defaultPath = '/home/';


    const ponerIcono = (estado) => {
      let icono = '';
      switch (estado) {
        case 'Cancelado':
          icono = (<SvgIcon component={Checked} viewBox="0 0 22 22" />);
          break;
          case 'Pagado':
          icono = (  <SvgIcon component={Close} viewBox="0 0 22 22" />);

            break;
            case 'En proceso':
        icono =  (  <SvgIcon component={Alert} viewBox="0 0 22 22" />);

              break;
              case 'Rechazado':
              icono =  ( <SvgIcon component={Rejected} viewBox="0 0 22 22" /> );

                break;
        default:
          icono =  '';
      }
      return icono;
    }



    const ponerDescarga = () => {
      if (! (semana.reportesMayores.length === 0) || ! (semana.reportesMenores.length === 0)) {
        return (
          <div className="column content-right">
              <Button variant="contained" style={{ backgroundColor: '#4CAF50', color: '#FFFFFF' }}
              onClick = {(e) => handledescargaSemanal(semana.semana)}>
                  <SvgIcon component={Excel} />
                  <span className="space">
                  </span>
                  Descargar Excel
              </Button>
          </div>
        );
      } else {
        return '';
      }

    }
    const openFile = (enlace) => {
      let host = window.location.host;
      const newWindow = window.open('https://' + host + '/build/'+enlace , '_blank', 'noopener,noreferrer')
      if (newWindow){
        newWindow.opener = null
      }
    }

    const handledescargaSemanal = (semana) => {
      let enlace = '';
      getExcelSemanal(credentials, estatus, tipoReporte)
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

    return (
      ( (! (semana.reportesMayores.length === 0) || ! (semana.reportesMenores.length === 0)) ? (
        <div key = {semana.semana}className="reporteSemanal">
             <div className="rowPrincipal">
                <div className="column">
                    <div className="title">{semana.title}</div>
                    <div className="subtitle">{semana.ano}</div>
                </div>
                <div className="column content-center">
                    <div className="divisor"></div>
                </div>
                { ponerDescarga() }

            </div>
            { (! (semana.reportesMayores.length === 0)) ? (
              <div >
              <div className="rowPrincipal">
                 <div className="column">

                     <div className="subtitle">Mayores a 45 días</div>
                 </div>
                 <div className="column content-center">
                     <div className="divisor"></div>
                 </div>

             </div>
              <div className="rowHeader">
                  <div className="column">REF. CLAIM</div>
                  <div className="column">LOCALIDAD</div>
                  <div className="column">TRANSPORTISTA</div>
                  <div className="column">TIPO DE DAÑO</div>
                  <div className="column">FECHA DE EMISIÓN</div>
                  <div className="column">ESTATUS</div>
              </div>

              { semana.reportesMayores.map((reporte) => {
                return (
                  <div key = {reporte.id} className="row" onClick={() => onClickReporte(reporte.id)}>
                      <div className="column">{reporte.claim}</div>
                      <div className="column">{reporte.localidad}</div>
                      <div className="column">{reporte.transportista}</div>
                      <div className="column">{reporte.tipo}</div>
                      <div className="column">{reporte.fecha}</div>
                      <div className="column content-icon">
                          <span>

                              {reporte.estatus}
                              </span>
                          <SvgIcon component={Back} viewBox="0 0 16 16" />
                      </div>
                  </div>
                );

              })}
              </div>
            ) : ''
          }



            { (! (semana.reportesMenores.length === 0)) ? (
              <div >
              <div className="rowPrincipal">
                 <div className="column">

                     <div className="subtitle">Menores a 45 días</div>
                 </div>
                 <div className="column content-center">
                     <div className="divisor"></div>
                 </div>

             </div>
              <div className="rowHeader">
                  <div className="column">REF. CLAIM</div>
                  <div className="column">LOCALIDAD</div>
                  <div className="column">TRANSPORTISTA</div>
                  <div className="column">TIPO DE DAÑO</div>
                  <div className="column">FECHA DE EVENTO</div>
                  <div className="column">ESTATUS</div>
              </div>

              { semana.reportesMenores.map((reporte) => {
                return (
                  <div key = {reporte.id} className="row" onClick={() => onClickReporte(reporte.id)}>
                      <div className="column">{reporte.claim}</div>
                      <div className="column">{reporte.localidad}</div>
                      <div className="column">{reporte.transportista}</div>
                      <div className="column">{reporte.tipo}</div>
                      <div className="column">{reporte.fecha}</div>
                      <div className="column content-icon">
                          <span>
                              {reporte.estatus}
                              </span>
                          <SvgIcon component={Back} viewBox="0 0 16 16" />
                      </div>
                  </div>
                );

              })}
              </div>
            ) : ''
          }


        </div>
      ) : (''))
    )
}

export default withRouter(ReporteSemanal);
