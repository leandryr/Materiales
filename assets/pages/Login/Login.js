import React , { useState  , useEffect  }   from 'react';
import './Login.scss';
import Logo from '../../components/Logo/Logo';
import Loader from '../../components/Loader/Loader';

import { TextField, Button, CardContent, Card } from '@material-ui/core';
import { Link } from "react-router-dom";

export default function Login(props) {
  let {onMessage} = props;
  const [name, handleName] = useState('');
  const [password, handlePassword] = useState('');
  const [message, handleMessage] = useState('');


  const handleLoginSubmit = (event) => {
    event.preventDefault();
    const {onLogin} = props;
    const credentials = {username: name, password:password}
    onLogin(credentials);
  }

  function handleNameChange(e){
    handleName(e.target.value);
  }
  function handlePasswordChange(e){
    handlePassword(e.target.value);
  }
  return(
        <div className="container">
            <Card className="card">

                <CardContent className="content">
                    <Logo className="logo"></Logo>
                    <span className="label">Materiales</span>
                    <form className="form-content" noValidate autoComplete="on">
                        <TextField type="email" id="email" label="Correo electrónico" variant="outlined"
                            className="email" size="small" value={name} onChange={handleNameChange} name="email" />
                        <TextField type="password" id="password" label="Contraseña" variant="outlined"
                            className="password" size="small" value = {password} onChange={handlePasswordChange} name="password"/>

                            { !!onMessage ? <span >{onMessage}</span> : '' }
                            <Button variant="contained" className="button" color="secondary" onClick={handleLoginSubmit} >
                                Iniciar sesión
                          </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    );
}
