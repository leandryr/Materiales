import React  from 'react'

export default class Lupa extends React.Component {
  render(){
    const lupa = {
    fill: "#b8b8b8"
    };
    return (
        <svg style={lupa} xmlns="http://www.w3.org/2000/svg" width="17.92" height="17.92" viewBox="0 0 17.92 17.92">
          <defs>

          </defs>
          <g transform="translate(0 0)"><path style={lupa} d="M17.7,16.648l-5.1-5.1a7.107,7.107,0,1,0-1.056,1.056l5.1,5.1A.747.747,0,1,0,17.7,16.648ZM7.093,12.7a5.6,5.6,0,1,1,5.6-5.6A5.606,5.606,0,0,1,7.093,12.7Z" transform="translate(0 -0.003)"/></g>
        </svg>
    );
  }
}
