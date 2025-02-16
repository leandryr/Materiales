import React,{useEffect, useState, useReducer}from 'react';
import {
    Button,
    DialogTitle,
    DialogContent,
    DialogActions,
    TextField, Select, InputLabel,
    FormControl, MenuItem, Switch
} from "@material-ui/core";
import './HistorialUsuarios.scss';
import SvgIcon from "@material-ui/core/SvgIcon";
import  Delete  from '../../img/delete.js';
import  Edit  from '../../img/edit.js';
import  TitleDelete from '../../img/delete_2.js';
import  TitleEdit  from '../../img/edit_2.js';
import { useDialog } from "../../context/DialogProvider";
import { editUser, deleteUser, deactivateUser } from '../../api/api.js';


const initialState = {
   id: '',
   usuario: '',
   correo: '',
   contracena: '',
   type: '',
   activo: true
 };


const confirmationReducer = (estado, action) => {
switch (action.type) {
case 'CHANGE_NAME': {
  return {
    ...estado,
    usuario: action.payload,
  };
}
case 'CHANGE_CORREO': {
  return {
    ...estado,
    correo: action.payload,

  };
}
case 'CHANGE_CONTRACENA': {
  return {
    ...estado,
    contracena: action.payload,
  };
}
case 'CHANGE_TYPE': {
  return {
    ...estado,
    type: action.payload,
  };
}
case 'SET_TYPE': {
  return {...estado,
    activo: action.payload.activo,
    usuario: action.payload.usuario,
    correo: action.payload.correo,
    contracena: action.payload.contracena,
    type: action.payload.type,
    id: action.payload.id,
  };
}
case 'EDIT_TYPE': {
  return estado;
}
case 'DELETE_TYPE': {
  return estado;
}
default: return estado;
}
}

function HistorialUsuarios(props) {
  const [estado, dispatch] = useReducer(confirmationReducer, initialState);

  const {
     id,
     usuario,
     correo,
     contracena,
     type,
     activo,
   } = estado ;

  const [idValue , setId] = useState('');
  const [usuarioValue , setUsuario] = useState('');
  const [correoValue , setCorreo] = useState('');
  const [contracenaValue , setContracena] = useState('');
  const [typeValue , setType] = useState('');
  const [accion , setAction] = useState('');
  const [tipo , setTipo] = useState('');
  const [helper , setHelper] = useState('');

  const [dialogOpen , setDialogOpen] = useState(false);
  const [send , setSend] = useState(false);



  const {usuarios, onUpdateList, credentials, onDeleteUser, onDeactivateUser} = props;
    const [openDialog, closeDialog] = useDialog();
    const [state, setState] = useState({
        checkedB: true,
    });

    const handleOpenDialog = (action,user)  => {
      setId(user.id);
      setUsuario(user.usuario);
      setCorreo(user.correo);
      setContracena(user.contracena);
      setType(user.type);

      dispatch({
          type: 'SET_TYPE',
          payload: user,
        });

      setAction(action);
      setDialogOpen(true);

    }

    useEffect(()=>{
      if(send){
        editUser(credentials, estado)
        .then((datos) => {
          onUpdateList(datos.validation.item);
        })
        .catch((e) => {

        });

      }
      setSend(false)
    },[send])

    useEffect(()=>{
      if(dialogOpen){
        onOpenDialog();
      }
      setDialogOpen(false)
    },[dialogOpen])

    const handleChange = (event) => {
      deactivateUser(credentials,event.target.id)
      .then((datos) => {
        onDeactivateUser(event.target.id);
      })
      .catch((e) => {

      });
    };

    const onCloseDialog = () =>{
      closeDialog();
    }

    const handleEditarUsuario =  () => {
       //onEditUsuario();
       //handleCloseDialog();
       dispatch({
           type: 'EDIT_TYPE'
         });

      closeDialog();
      setSend(true);

   }

   const handleDeleteUsuario =  () => {
      deleteUser(credentials,estado.id)
      .then((datos) => {
        onDeleteUser(estado.id);
      })
      .then(() => {
          closeDialog();
      })
      .catch((e) => {

      });

  }

const changeName = (e) => {
  dispatch({
      type: 'CHANGE_NAME',
      payload: e.target.value,
    });
}
const changeCorreo = (e) => {
  dispatch({
      type: 'CHANGE_CORREO',
      payload: e.target.value,
    });
}
const changeContracena = (e) => {
  dispatch({
      type: 'CHANGE_CONTRACENA',
      payload: e.target.value,
    });
}
const changeType = (e) => {
  dispatch({
      type: 'CHANGE_TYPE',
      payload: e.target.value,
    });
}

    const onOpenDialog = () => {


        const label = accion === 'delete'
            ? 'Confirma que deseas eliminar este usuario:'
            : 'Edita los datos de usuario:';
        const labelBtn = accion === 'delete'
            ? 'Eliminar'
            : 'Guardar';
        openDialog({
            children: (
                <div className="dialog">
                    <div className="dot"><SvgIcon component={accion === 'delete' ? TitleDelete : TitleEdit} viewBox="0 0 41 47" /></div>
                    <DialogTitle className="titleDialog">{label}</DialogTitle>
                    <DialogContent className="contentDialog">
                        {accion === 'delete' ? estado.usuario: null}
                        <div className={accion === 'delete' ? 'formContent' : ''}>
                            <div className="row">
                                <div className="column">
                                    <TextField id="NombreUsuario" label="Nombre de usuario" className="textField" variant="outlined" size="small"
                                    defaultValue = {estado.usuario}
                                    onChange = {changeName}/>
                                </div>
                                <div className="column">
                                    <TextField id="CorreoElectronico" label="Correo electrónico" className="textField" variant="outlined" size="small"
                                    defaultValue = {estado.correo}
                                    onChange = {changeCorreo}/>
                                </div>
                                <div className="column">
                                    <TextField id="Contrasena" label="Contraseña" className="textField" variant="outlined" size="small"
                                    defaultValue = {estado.contracena}
                                    onChange = {changeContracena}/>
                                </div>
                            </div>
                            <div className="row">

                                <div className="column">
                                    <FormControl variant="outlined" className="textField" size="small">
                                        <InputLabel htmlFor="outlined-tipoAdmin">Tipo de admin</InputLabel>
                                        <Select
                                            labelId="outlined-tipoAdmin"
                                            id="tipoAdmin"
                                            label="tipoAdmin"
                                            onChange={changeType}
                                            defaultValue = {estado.type}
                                        >
                                          <MenuItem value={'Administrador'}>Administrador</MenuItem>
                                          <MenuItem value={'Capturista'}>Capturista</MenuItem>
                                          <MenuItem value={'Personal'}>Personal</MenuItem>
                                        </Select>
                                    </FormControl>
                                </div>
                                <div className="column">
                                    <Button variant="contained" color="secondary" className="button"
                                     onClick = { () => handleEditarUsuario() }
                                       >
                                        Guardar
                                      </Button>
                                </div>
                            </div>
                        </div>
                    </DialogContent>
                    <DialogActions className={accion === 'delete' ? '' : 'actions'}>
                        <Button variant="contained" style={{ backgroundColor: '#FF2626', color: '#ffffff', width: '270px' }}
                        onClick={() => handleDeleteUsuario()
                        }>
                            {labelBtn}
                        </Button>
                        <Button variant="contained" style={{ backgroundColor: '#192E47', color: '#ffffff', width: '270px' }}
                        onClick={() => closeDialog()}>
                            Cancelar
                        </Button>
                    </DialogActions>
                </div>
            )
        });
    };
    return (
        <div className="historialUsuarios">
            <div className="rowPrincipal">
                <div className="column">
                    <div className="title">Usuarios Ramirez Valle</div>
                </div>
            </div>
            <div className="rowHeader">
                <div className="column">USUARIO</div>
                <div className="column">CORREO ELECTRÓNICO</div>
                <div className="column">CONTRASEÑA</div>
                <div className="column">TIPO DE ADMIN</div>
                <div className="column cl-50">EDITAR</div>
                <div className="column cl-50">ELIMINAR</div>
                <div className="column cl-50">PERMISOS</div>
            </div>
            { usuarios.map((usuario) => (
              <div className = "row"  key={usuario.id} id={usuario.id} >

                <
                div className = "column" > {usuario.usuario} < /div> <
                div className = "column" > {usuario.correo} < /div> <
                div className = "column" > {usuario.contracena} < /div> <
                div className = "column" > {usuario.type} < /div> <
                div className = "column cl-50 content-icon" onClick={() => { handleOpenDialog('edit', usuario ); }} >
                  <SvgIcon component={Edit}  />
                </div>
                <div className = "column cl-50 content-icon"  onClick = {() => {handleOpenDialog('delete', usuario);}} >
                  <SvgIcon component = {Delete}/> <
                /div>
                <div className="column cl-50 content-icon">
                    <Switch
                        id={usuario.id}
                        checked={usuario.activo}
                        onChange={handleChange}
                        color="secondary"
                        name="checkedB"
                        inputProps={{ 'aria-label': 'primary checkbox' }}
                    />
                </div>

             </div >
            ))}

        </div>
    );
}

export default HistorialUsuarios
