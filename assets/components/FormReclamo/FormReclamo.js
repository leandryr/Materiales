import React,{useState, useEffect} from 'react';
import {
    TextField, Select, InputLabel,
    FormControl, MenuItem, Button , DialogTitle, DialogContent
} from '@material-ui/core';
import './FormReclamo.scss';
import { sendEmail, deleteFile } from '../../api/api.js';

import  Close  from '../../img/close_circle.js';
import  Checked  from '../../img/checked_bg.js';
import  Alert  from '../../img/alert.js';

import { useDialog } from "../../context/DialogProvider";
import { uploadFile  } from '../../api/api.js';
import SvgIcon from "@material-ui/core/SvgIcon";
import { withRouter } from 'react-router-dom';




function FormReclamo(props) {
  const [correo,handleCorreo] = useState('');
  const [cccorreo,handleCCCorreo] = useState('');
  const [asunto,handleAsunto] = useState('');
  const [mensaje,handleMensaje] = useState('');
  const [nameFile,handleNameFile] = useState('');
  const [selectedFile,handleSelectFile] = useState('');

  const {credentials, onLogOut, history} = props;

    const [openDialog, closeDialog] = useDialog();
    const [ envio, setEnvio ]  = useState(false);


    let dialogTimeOut  = 0;
    let dialogTimeIn  = 0;

    const onOpenDialog = (action) => {

        const label = action === 'correct' ? ('Correo enviado correctamente') : ( action === 'enviando'  ? 'Enviando Correo' :'Upss al parecer algo salió mal.' )
        openDialog({
            children: (
                <div className={action === 'correct' ? 'dialogExcel bt-green' :  ( action === 'enviando'  ? 'dialogExcel bt-blue' :'dialogExcel bt-red'  )  }>
                    <DialogTitle className="titleDialog">{label}</DialogTitle>
                    <DialogContent className="contentDialog">
                        <div className="content-icon">
                            <SvgIcon component={action === 'correct' ? Checked :  ( action === 'enviando'  ? Alert :Close  )  } viewBox="0 0 109 109" />
                        </div>
                    </DialogContent>
                </div>
            )
        });
    };

  const handleSelectedFile = (event) => {
    handleSelectFile(event.target.files[0]);
    handleNameFile(event.target.files[0].name);
  }

  const handleEnviarCorreo = (event) => {
    const {credentials, onLogOut} = props;

    if(!!correo && !! mensaje && !!asunto ){
        handleOpenCloseDialog(1);
        var formData = new FormData();

        formData.append('correo', correo);
        formData.append('mensaje', mensaje);
        formData.append('asunto', asunto);

        if(!!cccorreo){
          formData.append('cccorreo', cccorreo);
        }else {
          formData.append('cccorreo', '');
        }

        if(!!nameFile && !! selectedFile){
          formData.append('file', selectedFile, nameFile);
        }

        sendEmail(credentials, formData )
        .then((data)=>{
          if(data.validation.fileName){
            deleteFile(credentials, data.validation.fileName)
          }
          handleOpenCloseDialog(2);

        })

        .catch((error) => {
          handleOpenCloseDialog(3);
        });
    }else {
    handleOpenCloseDialog(3);

    }

  }

  const handleOpenCloseDialog = (envioExitoso) => {
    setEnvio(envioExitoso);

    clearTimeout(dialogTimeOut);
    dialogTimeOut = setTimeout(() => {
      setEnvio(0);
    }, 3000)
  }

  useEffect( () => {
    if(envio ===1){
      onOpenDialog('enviando');
    }else if (envio ===2) {

      closeDialog();
      clearTimeout(dialogTimeIn);
      dialogTimeIn = setTimeout(() => {

        handleAsunto('');
        handleCorreo('');
        handleMensaje('');
        handleCCCorreo('');
        handleNameFile('');
        handleSelectFile('');
        onOpenDialog('correct');
        setEnvio(0);
      }, 300);


    }else if (envio ===3) {
      closeDialog();
      clearTimeout(dialogTimeIn);
      dialogTimeIn = setTimeout(() => {
          onOpenDialog('error');
          setEnvio(0);
      }, 300);
    }
    else{
      clearTimeout(dialogTimeOut);
      dialogTimeOut = setTimeout(() => {
        closeDialog();
      }, 3000);
    }
  },
  [envio]);




    return (
        <form className="formReclamo" noValidate autoComplete="off">
            <div className="row">
                <div className="column">
                    <TextField id="CorreoElectronico" label="Correo electronico" className="textField" variant="outlined" size="small"
                    value = {correo}
                    onChange = {(e) => {handleCorreo(e.target.value)}}/>
                </div>
                <div className="column">
                    <TextField id="CCCorreoElectronico" label="CC Correo electrónico" className="textField" variant="outlined" size="small"
                    value = {cccorreo}

                    onChange = {(e) => {handleCCCorreo(e.target.value)}} />
                </div>
                <div className="column">
                    <TextField id="Asunto" label="Asunto" className="textField" variant="outlined" size="small"
                    value = {asunto}

                    onChange = {(e) => {handleAsunto(e.target.value)}}/>
                </div>
                <div className="column">
                </div>
            </div>
            <div className="row">
                <TextField
                    id="mensaje"
                    label="Mensaje para el receptor"
                    className="textField"
                    multiline
                    rows={4}
                    variant="outlined"
                    value = {mensaje}
                    onChange = {(e) => {handleMensaje(e.target.value)}}
                />
            </div>
            <div className="row">
                <div className="reclamo-label">Selecciona el botón de buscar archivo y elige el archivo que deseas enviar.</div>
            </div>
            <div className="upload-content">

                <TextField id="nombreArchivo" label="Nombre del archivo" variant="outlined" size="small" className="input"
                  value= {nameFile}/>
                <input
                  hidden
                  id="contained-button-file"
                  multiple
                  type="file"
                  onChange={handleSelectedFile}
                />
                <label htmlFor="contained-button-file">
                  <Button variant="contained" color="primary" className="button" component="span" >
                      Buscar archivo
                  </Button>
                </label>
            </div>
            <div className="row">
                <Button variant="contained" color="secondary" className="button"
                onClick = {handleEnviarCorreo}>
                    Enviar
                </Button>
            </div>
        </form>
    )
}

export default withRouter(FormReclamo)
