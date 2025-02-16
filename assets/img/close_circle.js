import React  from 'react'

export default class Close extends React.Component {
  render(){
const closebga = {
  fill: "#d71d0f"
}

const closebgb = {
  fill : "#fff"
}

const b = {
  fill: "#fff",
  fontSize: "18px",
  fontFamily: "Montserrat-Light, Montserrat",
  fontWeight: "300"
}


    return (
      <svg xmlns="http://www.w3.org/2000/svg" width="109" height="109" viewBox="0 0 109 109">
        <defs>

        </defs>
        <g transform="translate(-0.375 0.002)"><circle style={closebga}  cx="54.5" cy="54.5" r="54.5" transform="translate(0.375 -0.002)"/><path  style={closebgb}   d="M188.635,192.485a3.835,3.835,0,0,1-2.722-1.128l-38.5-38.5a3.849,3.849,0,0,1,5.443-5.444l38.5,38.5a3.85,3.85,0,0,1-2.721,6.572Zm0,0" transform="translate(-116.189 -114.283)"/><path style={b}   d="M150.135,192.484a3.85,3.85,0,0,1-2.721-6.572l38.5-38.5a3.849,3.849,0,1,1,5.444,5.443l-38.5,38.5A3.835,3.835,0,0,1,150.135,192.484Zm0,0" transform="translate(-116.188 -114.282)"/></g>
      </svg>
    );
  }
}
