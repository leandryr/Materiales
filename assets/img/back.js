import React  from 'react'

export default class Back extends React.Component {
  render(){
    const x = {
      fill: "#192e47"
    }
    return (

      <svg xmlns="http://www.w3.org/2000/svg" style={x} width="9.173" height="15.613" viewBox="0 0 9.173 15.613">
        <defs>

        </defs>
        <g transform="translate(9.173 15.613) rotate(180)">
          <g transform="translate(0 0)">
            <path style={x}  d="M3.082,7.81,8.923,1.969a.856.856,0,0,0,0-1.208L8.411.25A.855.855,0,0,0,7.2.25L.249,7.2a.862.862,0,0,0,0,1.212L7.2,15.364a.856.856,0,0,0,1.208,0l.512-.512a.855.855,0,0,0,0-1.208Z"/>
          </g>
        </g>
      </svg>
    );
  }
}
