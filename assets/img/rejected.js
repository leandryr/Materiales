import React  from 'react'

export default class Rejected extends React.Component {
  render(){
    const rejectedA = {
    fill: "#d71d0f",
    };
    const rejectedB = {
      fill: "#fff"
    }

    return (
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
        <defs>

        </defs><circle style={rejectedA} cx="11" cy="11" r="11"/><rect style={rejectedB} width="12" height="2" transform="translate(5 10)"/></svg>
    );
  }
}
