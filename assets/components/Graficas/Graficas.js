import React , {useEffect } from 'react';
import { Doughnut } from 'react-chartjs-2';
import './Graficas.scss';

function numberWithCommas(x) {
  if(!!x){
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }
    return '0.00';
}


  var stringToColour = function(str) {
    var hash = 0;
    for (var i = 0; i < str.length; i++) {
      hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    var colour = '#';
    for (var i = 0; i < 3; i++) {
      var value = (hash >> (i * 8)) & 0xFF;
      colour += ('00' + value.toString(16)).substr(-2);
    }
    return colour;
  }


const Graficas = (props) => {
  const {aceptado,
  reclamado,
  proceso,
  cancelado,
  recuperado,
  rechazado,
  transportistasGrafica,
total
 }= props;
 let labels = [];
 let values = [];
 let colors = [];
 useEffect(() =>{
   if (!!transportistasGrafica) {
     for (var key in transportistasGrafica) {
       values.push(transportistasGrafica[key]);
       labels.push(key);
       colors.push(stringToColour(key));
     }
   }else{

   }
 },[transportistasGrafica]);

    const data = {
      labels: labels,
        datasets: [{
            label: 'cantiad',
            data: values,
            hoverOffset: 4,
            backgroundColor : colors
        }]
    };
    const options = {
        responsive: true,
        plugins: {
          legend: {
            display: false,
          },
          tooltips: {
            enabled: false
         },
          title: {
            display: false,
            text: 'Chart.js Doughnut Chart'
          }
        }
    };
    const dataAceptado = {
        datasets: [{
            label: 'Monto Aceptado',
            data: [aceptado],
            backgroundColor: [
                '#4BC08D',
            ],
            hoverOffset: 4
        }]
    };
    const dataReclamado = {
        datasets: [{
            label: 'Monto Reglamado',
            data: [reclamado],
            backgroundColor: [
                '#4B87C0',
            ],
            hoverOffset: 4
        }]
    };
    const dataProceso = {
        datasets: [{
            label: 'Monto en Proceso',
            data: [proceso],
            backgroundColor: [
                '#F5C816',
            ],
            hoverOffset: 4
        }]
    };
    const dataCancelado = {
        datasets: [{
            label: 'Mondo Cancelado',
            data: [cancelado],
            backgroundColor: [
                '#555555',
            ],
            hoverOffset: 4
        }]
    };
    const dataRecuperado = {
        datasets: [{
            label: 'Monto Recuperado',
            data: [recuperado],
            backgroundColor: [
                '#804BC0',
            ],
            hoverOffset: 4
        }]
    };
    const dataRechazado = {
        datasets: [{
            label: 'Monto Rechazado',
            data: [rechazado],
            backgroundColor: [
                '#D53535',
            ],
            hoverOffset: 4
        }]
    };
    return (
        <div className="graficas">
          <div className="doughnut">
            <div className="contenedor">

                  <Doughnut
                  data = {data}
                  options = {options}
                   />
                  <div className="cantidad">
                      {total} Resultados
            </div>
            </div>

          </div>

            <div className="wrapper">
                <div className="row">
                    <div className="column">
                        <div className="graph">
                            <Doughnut data={dataAceptado} />
                        </div>
                        <div>
                            <div className="monto">
                                Monto aceptado
                            </div>
                            <div className="cantidad">
                                ${numberWithCommas(aceptado)}
                            </div>
                        </div>
                    </div>
                    <div className="column">
                        <div className="graph">
                            <Doughnut data={dataReclamado} />
                        </div>
                        <div>
                            <div className="monto">
                                Monto reclamado
                            </div>
                            <div className="cantidad">
                                ${numberWithCommas(reclamado)}
                            </div>
                        </div>
                    </div>
                    <div className="column">
                        <div className="graph">
                            <Doughnut data={dataProceso} />
                        </div>
                         <div>
                            <div className="monto">
                                Monto en proceso
                            </div>
                            <div className="cantidad">
                                ${numberWithCommas(proceso)}
                            </div>
                        </div>
                    </div>
                </div>
                <div className="row">
                    <div className="column">
                        <div className="graph">
                            <Doughnut data={dataCancelado} />
                        </div>
                        <div>
                            <div className="monto">
                                Monto cancelado
                            </div>
                            <div className="cantidad">
                                ${numberWithCommas(cancelado)}
                            </div>
                        </div>
                    </div>
                    <div className="column">
                        <div className="graph">
                            <Doughnut data={dataRecuperado} />
                        </div>
                        <div>
                            <div className="monto">
                                Monto recuperado
                            </div>
                            <div className="cantidad">
                                ${numberWithCommas(recuperado)}
                            </div>
                        </div>
                    </div>
                    <div className="column">
                        <div className="graph">
                            <Doughnut data={dataRechazado} />
                        </div>
                        <div>
                            <div className="monto">
                                Monto rechazado
                            </div>
                            <div className="cantidad">
                                $ {numberWithCommas(rechazado)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Graficas
