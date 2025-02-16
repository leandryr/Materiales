import React, {useState} from 'react';
import Pagination from '@material-ui/lab/Pagination';
import './HistorialRegistros.scss';
import SvgIcon from "@material-ui/core/SvgIcon";
import  Back  from '../../img/back.js';
import  Checked  from '../../img/checked.js';
import  Excel  from '../../img/excel.js';
import  Close from '../../img/close.js';
import  Alert from '../../img/alert.js';
import  Rejected  from '../../img/rejected.js';
import Button from '@material-ui/core/Button';
import { withRouter } from 'react-router-dom';
import { getRegistro, getRegistrosBusqueda } from '../../api/api.js';


const rango = (pagina,tot,limit) => {
  const primer= ( (pagina -1)  * limit) +1;
  let segundo = 0;
   ((primer+ limit -1) < tot? (segundo = primer + limit -1 ) : (segundo = tot) );
  return `${primer} - ${segundo}` ;
}

function HistorialRegistros(props) {
    const { onClickRegistro,title, history , total, currentPage, maxPages, registros, limite, onDescargarExcerRegistros, credentials, filtros} = props;

    const [totalState, setTotal] = useState(total);
    const [currentPageState, setCurrentPage] = useState(currentPage);
    const [maxPagesState, setMaxPages] = useState(maxPages);
    const [registrosState, setRegistros] = useState(registros);
    const [limiteState, setLimite] = useState(limite);

    const defaultPath = '/home/';

    const handleListItemClick = (url) => {
      getRegistro(credentials,url)
      .then((datos) => {
        onClickRegistro(datos.item);
      })
    };

    const handlePaginator = (event, value) => {
      let filt = { ... filtros, pagina:value};
      getRegistrosBusqueda(credentials, filt)
      .then((response) =>{
          console.log("pagination");
          setCurrentPage(response.validation.pagina);
          setMaxPages(response.validation.maxPages);
          setRegistros(response.validation.items);
          setLimite(response.validation.limite);
      })
      .catch((error) => {
        console.log(error);
        });
    };



    return (
        <div className="historialRegistros">
            <div className="rowPrincipal">
                <div className="column">
                    <div className="title">{title}</div>
                    <div className = "subtitle" > {rango(currentPageState,totalState,limiteState)} de {totalState} RESULTADOS < /div>
                </div>

                <div className="column content-center">
                  <
                  Pagination
                  count = {maxPagesState}
                  color = "primary"
                  page={currentPageState}
                  onChange={handlePaginator}
                  boundaryCount={2}
                   / >
                </div>
                <div className="column content-right"  >
                    <Button variant="contained" style={{ backgroundColor: '#4CAF50', color: '#FFFFFF' }}
                        onClick = {(e) => onDescargarExcerRegistros()}
                        >
                        <SvgIcon component={Excel} />
                        <span className="space"></span>
                        Descargar Excel
                    </Button>
                </div>
            </div>
            <div className="rowHeader">
                <div className="column">REF. CLAIM</div>
                <div className="column">LOCALIDAD</div>
                <div className="column cl-250">TRANSPORTISTA</div>
                <div className="column">TIPO DE DAÑO</div>
                <div className="column">FECHA DE EVENTO</div>
                <div className="column">ESTATUS</div>
            </div>
            { (!!registrosState) ?  registrosState.map((registro) =>  {
                  return(
                    <div key = {registro.id} className="row" onClick={(event) => handleListItemClick(registro.id)}>
                        <div className="column">{registro.referencia}</div>
                        <div className="column">{registro.localidad}</div>
                        <div className="column">{registro.transportista}</div>
                        <div className="column">{registro.tipo}</div>
                        <div className="column">{registro.fecha}</div>
                        <div className="column content-icon">
                            <span>

                                    {registro.estatus}
                                </span>
                            <SvgIcon component={Back} viewBox="0 0 16 16" />
                        </div>
                    </div>
                  );
            }) : ('')
            }

        </div>
    )
}

export default withRouter(HistorialRegistros);
