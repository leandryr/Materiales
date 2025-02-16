import React  from 'react'

export default class BackBtn extends React.Component {
  render(){
    const back = {
    fill: "#346298"
    };
    const a = {
    fill: "#fff"
    };
    return (

      <svg xmlns="http://www.w3.org/2000/svg"  width="29.849" height="29.85" viewBox="0 0 29.849 29.85">
        <defs>
        </defs>
        <g transform="translate(-0.002)"><path style={back}  d="M14.927,29.85A14.925,14.925,0,1,0,0,14.925,14.942,14.942,0,0,0,14.927,29.85Zm0-27.807A12.882,12.882,0,1,1,2.045,14.925,12.9,12.9,0,0,1,14.927,2.043Z"/>
        <path style={back}  d="M57.005,73.534A1.022,1.022,0,1,0,58.45,72.09l-3.925-3.925H65.892a1.022,1.022,0,0,0,0-2.043H54.524l3.926-3.926a1.022,1.022,0,0,0-1.445-1.445l-5.67,5.67a1.022,1.022,0,0,0,0,1.445Z" transform="translate(-44.083 -52.217)"/></g>
      </svg>
    );
  }
}
