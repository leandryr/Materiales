import React, {useEffect} from 'react'
import "./Header.scss"

function Header(props) {
  const {credentials, onLogOut} = props;
  useEffect(()=> {
    if(credentials.rol == ''){
      onLogOut();
    }
  },[])
    return (
        <div className="header">
            <div className="wrapName">
                <div className="name">{credentials.name}</div>
                <div className="position">
                { (credentials.rol === 'ROLE_ADMINISTRADOR') ? ('Administrador General') :
                  ( (credentials.rol === 'ROLE_CAPTURISTA') ? ( 'Capturiasta') :
                   ((credentials.rol === 'ROLE_PERSONAL') ? ('Personal GM'):
                   ('' ) ))
                  }
                </div>
            </div>
        </div>
    )
}

export default Header
