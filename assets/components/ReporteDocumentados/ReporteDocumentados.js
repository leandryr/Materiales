import React from 'react';
import './ReporteDocumentados.scss';
import SvgIcon from "@material-ui/core/SvgIcon";
import  Back  from '../../img/back.js';
import  Checked  from '../../img/checked.js';
import  Excel  from '../../img/excel.js';
import  Close  from '../../img/close.js';
import  Alert  from '../../img/alert.js';
import  Rejected  from '../../img/rejected.js';
import Button from '@material-ui/core/Button';
import { withRouter } from 'react-router-dom';
import { getExcelDocumentados , deleteFile} from '../../api/api.js';


function ReporteDocumentados(props) {
    const {  history, documentados, credentials, onClickReporteDocumentado} = props;

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
    const ponerDescarga = (len, semana) => {
      if (! (len === 0)) {
        return (
          <div className="column content-right">
              <Button variant="contained" style={{ backgroundColor: '#4CAF50', color: '#FFFFFF' }}
              onClick = {(e) => handledescargaDocumentados}>
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
      const newWindow = window.open('http://' + host + '/build/'+enlace , '_blank', 'noopener,noreferrer')
      if (newWindow){
        newWindow.opener = null
      }
    }

    const handleDescargaDocumentados = () => {
      let enlace = '';
      getExcelDocumentados(credentials)
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

    const ponerFecha = (fecha) => {
      let fechaString = '';

      if(!!fecha){

        //fechaString = fecha.toLocaleDateString('es-MX');
        fechaString = fecha;
      }

      return fechaString;
    }

    return (
        <div className="reporteDocumentados">

          <div className="rowPrincipal">
              <div className="column">
                  <div className="title">{'En documentacion'}</div>
                  <div className = "subtitle" >{(!!documentados) ? (documentados.length) : '0'}  en Documentacion  < /div>
              </div>

              <div className="column content-center">

              </div>

              <div className="column content-right"  >
                  <Button variant="contained" style={{ backgroundColor: '#4CAF50', color: '#FFFFFF' }}
                  onClick={() => handleDescargaDocumentados()}
                      >
                      <SvgIcon component={Excel} />
                      <span className="space"></span>
                      Descargar
                  </Button>
              </div>
          </div>

          <div className="rowHeader">
              <div className="column">REF. CLAIM</div>
              <div className="column">LOCALIDAD</div>
              <div className="column">CODIGO DE DAÑO</div>
              <div className="column">FECHA DE NOTIFICACIÓN RV</div>
              <div className="column">DOCUMENTACIÓN FALTANTE</div>
              <div className="column">ESTATUS</div>
          </div>

            {(!!documentados) ? (documentados.map((documentado)=>{
                   return (
                     <div key = {documentado.id} className="row" onClick={() => onClickReporteDocumentado(documentado.id)}>
                         <div className="column">{documentado.claim}</div>
                         <div className="column">{documentado.localidad}</div>
                         <div className="column">{documentado.codigo}</div>
                         <div className="column"> {ponerFecha(documentado.fecha)}</div>
                         <div className="column">{documentado.documentacionFaltante}</div>
                         <div className="column content-icon">
                             <span>
                                 {documentado.estatus}
                                 </span>
                             <SvgIcon component={Back} viewBox="0 0 16 16" />
                         </div>
                     </div>
                   );
            })
          ) : ('')}
        </div>
    )
}

export default withRouter(ReporteDocumentados);
