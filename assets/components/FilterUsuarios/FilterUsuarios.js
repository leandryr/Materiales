import React, {useState, useEffect} from 'react'
import {
    TextField, Select, InputLabel,
    FormControl, MenuItem, Button
} from '@material-ui/core';
import './FilterUsuarios.scss';
import { newUser  } from '../../api/api.js';



function FilterUsuarios(props) {
  const [nombreField, setNombreField] = useState('');
  const [correoField, setCorreoField] = useState('');
  const [contracenaField, setContracenaField] = useState('');
  const [tipoField, setTipoField] = useState('');
  const [listo, setListo] = useState(false);
  const {credentials, onUsuarioNuevo} = props;

  const clearData = () => {
    setNombreField('');
    setCorreoField('');
    setContracenaField('');
    setTipoField('');
  }
  const handleUsuarioNuevo = (event) => {
    event.preventDefault();
    if(listo){

      const data =  {
        name: nombreField,
        email: correoField,
        password: contracenaField,
        type:tipoField
      };
      newUser(credentials, data)
      .then((datos) => {
        onUsuarioNuevo(datos.validation.item);
        clearData();

      })
      .catch((e) => {
        console.log(e);
      });

    }else {
      console.log("Llenar todos los campos");
    }

  }

  useEffect(() => {
    if(!!nombreField && !!correoField && contracenaField && tipoField){
      setListo(true);
    }
  }, [nombreField, correoField, contracenaField, tipoField])

    return (
        <div className="filterUsuarios">
            <div className="title">Agregar usuarios</div>
            <div className="row">
                <div className="column">
                    <TextField id="NombreUsuario" label="Nombre de usuario" className="textField" variant="outlined" size="small" value = {nombreField} onChange = { (e) => {setNombreField(e.target.value)}}/>
                </div>
                <div className="column">
                    <TextField id="CorreoElectronico" label="Correo electrónico" className="textField" variant="outlined" size="small" value = {correoField} onChange = { (e) => { setCorreoField (e.target.value) }} />
                </div>
                <div className="column">
                    <TextField id="Contrasena" label="Contraseña" className="textField" variant="outlined" size="small" value = {contracenaField} onChange = { (e) => {setContracenaField(e.target.value)} } />
                </div>
                <div className="column">
                    <FormControl variant="outlined" className="textField" size="small">
                        <InputLabel htmlFor="outlined-tipoAdmin">Tipo de admin</InputLabel>
                        <Select
                            labelId="outlined-tipoAdmin"
                            id="tipoAdmin"
                            label="tipoAdmin"
                            value = {tipoField}
                            onChange = {(e) => {setTipoField(e.target.value)} }
                        >
                            <MenuItem value={'Administrador'}>Administrador</MenuItem>
                            <MenuItem value={'Capturista'}>Capturista</MenuItem>
                            <MenuItem value={'Personal'}>Personal</MenuItem>
                        </Select>
                    </FormControl>
                </div>
                <div className="column">
                    <Button variant="contained" color="secondary" className="button" onClick = {handleUsuarioNuevo}>
                        Guardar
                    </Button>
                </div>
            </div>
        </div>
    )
}

export default FilterUsuarios;
