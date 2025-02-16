import React , {useEffect} from 'react';
import SvgIcon from "@material-ui/core/SvgIcon";
import { withRouter } from 'react-router-dom';
import './ReporteDocumentado.scss';
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
import { getExcelDocumentado , deleteFile, ingresarDocumentado, getRegistro} from '../../api/api.js';


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

function ReporteDocumentado(props) {
    const { history, doc, credentials , onClickEdit,  onClickRegistro} = props;
    const defaultPath = '/home/';
    const handleListItemClick = (url) => {
        history.push(`${defaultPath}${url}`);
    };

    const openFile = (enlace) => {
      let host = window.location.host;
      const newWindow = window.open('http://' + host + '/build/'+enlace , '_blank', 'noopener,noreferrer')
      if (newWindow){
        newWindow.opener = null
      }
    }

    const handleDescargaReporte = () => {

      let enlace = '';
      getExcelDocumentado(credentials, doc.idD)
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

    const handleMoverRegistro = () => {
      ingresarDocumentado(credentials, doc.idD)
      .then((data) => {
          onClickRegistro(data.validation.item);
      })
      .catch((e) =>{

      })
    }

    const handleEditDocumentado = () => {
      onClickEdit();
    }

    const goBack = () => {
      history.push('/home/reportes');
    }

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

    const ponerFecha = (fecha) => {
      let fechaString = '';

      if(!!fecha){

        //fechaString = fecha.toLocaleDateString('es-MX');
        fechaString = fecha;
      }

      return fechaString;
    }


    return (
        <div className="reporteDocumentado">
            <div className="content-icon-header" >
              <div onClick = {()=> goBack()}>
                  <span >
                  <SvgIcon component={Regresar} viewBox="0 0 30 30" />
                   Regresar
                  </span>
                </div>

                <div style={{ display: 'flex', width: '100%', justifyContent: 'flex-end', alignItems: 'center' }}>


                  <Button variant="contained" color="secondary" style={{marginRight: '5.625rem' }}
                  onClick = {()=> handleMoverRegistro()}>
                      Ingresar a Registros
                  </Button>

                    <Button variant="contained" style={{ backgroundColor: '#4CAF50', color: '#FFFFFF', marginRight: '5.625rem' }}
                    onClick = {()=> handleDescargaReporte()}>
                        <SvgIcon component={Excel} />
                        <span className="space"></span>
                        Descargar Excel
                    </Button>

                    {(credentials.rol === 'ROLE_ADMINISTRADOR' || credentials.rol === 'ROLE_CAPTURISTA') ? (
                      <div onClick = {()=> handleEditDocumentado()}>
                      <SvgIcon component={Edit} viewBox="0 0 25 25" />
                      </div>
                                  ):
                                  ('')}

                </div>
            </div>


            <div className="row">
                <div className="column direction">
                    <div className="miniTitle">Referencia Claim:</div>
                    <div className="miniValue">{doc.claimD}</div>
                </div>

                <div className="column direction">
                    <div className="miniTitle">Localidad:</div>
                    <div className="miniValue">{doc.localidadD}</div>
                </div>

                <div className="column direction">
                    <div className="miniTitle">Codigo:</div>
                    <div className="miniValue">{doc.codigo}</div>
                </div>

                <div className="column direction">
                    <div className="miniTitle">Planta:</div>
                    <div className="miniValue">{doc.plantaD}</div>
                </div>

            </div>
            <div className="row">
                <div className="column direction">
                <div className="miniTitle">Número de parte:</div>
                <div className="miniValue">{doc.numero}</div>

                </div>
                <div className="column direction">
                <div className="miniTitle">Cantidad de piezas:</div>
                <div className="miniValue">{doc.cantidad}</div>


                </div>
                <div className="column direction">
                <div className="miniTitle">Fecha de notificación RV:</div>
                <div className="miniValue">{ponerFecha(doc.fechaNotificacion)}</div>


                </div>
                <div className="column direction">
                <div className="miniTitle">Valor de perdida sin flete:</div>
                <div className="miniValue">$ {numberWithCommas(doc.perdidaSinFlete)}</div>


                </div>
            </div>
            <div className="row">
                <div className="column direction">
                <div className="miniTitle">Valor de perdida con flete:</div>
                <div className="miniValue">$ {numberWithCommas(doc.perdidaConFlete)}</div>



                </div>
                <div className="column direction">
                <div className="miniTitle">Documentacion Faltante:</div>
                <div className="miniValue">{doc.documentacionFaltante}</div>


                </div>
                <div className="column direction">
                <div className="miniTitle">Área y Responsable:</div>
                <div className="miniValue">{doc.areaD}</div>


                </div>
                <div className="column direction">
                <div className="miniTitle">Estatus:</div>
                <div className="miniValue"> {doc.estatusD}</div>


                </div>
            </div>
        </div>
    )
}

export default withRouter(ReporteDocumentado);
