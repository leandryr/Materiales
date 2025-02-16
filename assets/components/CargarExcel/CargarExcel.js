import React, {useState, useEffect} from 'react';
import { TextField, Button, DialogTitle, DialogContent } from '@material-ui/core';
import SvgIcon from "@material-ui/core/SvgIcon";
import './CargarExcel.scss';
import  Excel  from '../../img/excel.js';
import  Close  from '../../img/close_circle.js';
import  Checked  from '../../img/checked_bg.js';
import  Alert  from '../../img/alert.js';
import  Loader  from '../Loader/Loader.js';


import { useDialog } from "../../context/DialogProvider";
import { uploadFile  } from '../../api/api.js';

function CargarExcel(props) {
  const {credentials} = props;

    const [openDialog, closeDialog] = useDialog();
    const [ selectedFiles, setSelectedFiles ]  = useState(null);
    const [ nameFile, setNameFile ]  = useState(null);
    const [ archivoCargado, setArchivoCargado ]  = useState(false);
    let dialogTimeOut  = 0;
    let dialogTimeIn  = 0;
    const onOpenDialog = (action) => {

        const label = action === 'correct' ? ('Archivo cargado correctamente') : ( action === 'enviando'  ? 'Cargando archivo' :'Upss al parecer algo salió mal.' )
        openDialog({
            children: (
                <div className={action === 'correct' ? 'dialogExcel bt-green' :  ( action === 'enviando'  ? 'dialogExcel bt-blue' :'dialogExcel bt-red'  )  }>
                    <DialogTitle className="titleDialog">{label}</DialogTitle>
                    <DialogContent className="contentDialog">

                        {action === 'correct' ?
                        <div className="content-icon">
                          <SvgIcon component={Checked} viewBox="0 0 109 109" />
                            </div>
                             :
                        ( action === 'enviando'  ? (
                          <div className="loader">
                            <Loader />
                              </div>

                        )
                          :
                          <div className="content-icon">
                          <SvgIcon component={Close} viewBox="0 0 109 109" />
                          </div>
                         )  }

                    </DialogContent>
                </div>
            )
        });
    };

    const onSelectFile = (event) => {
      setNameFile(event.target.files[0].name);
      setSelectedFiles(event.target.files);
    }

  const   onCargar = () =>{
      if(!!selectedFiles){
        handleOpenCloseDialog(1);


          var formData = new FormData();
            formData.append('currentFile', selectedFiles[0], nameFile);
          uploadFile(credentials, formData )
          .then((data)=>{
            handleOpenCloseDialog(2);
          })
          .catch((error) => {
            handleOpenCloseDialog(3);
          });

      }else {
        handleOpenCloseDialog(3);

      }
    }

    const handleOpenCloseDialog = (cargaExitosa) => {
        setArchivoCargado(cargaExitosa);
    }

    useEffect( () => {
      if(archivoCargado ===1){
        onOpenDialog('enviando');
      }else if (archivoCargado ===2) {

        closeDialog();
        clearTimeout(dialogTimeIn);
        dialogTimeIn = setTimeout(() => {
            onOpenDialog('correct');
            setArchivoCargado(0);
        }, 300);


      }else if (archivoCargado ===3) {
        closeDialog();
        clearTimeout(dialogTimeIn);
        dialogTimeIn = setTimeout(() => {
            onOpenDialog('error');
            setArchivoCargado(0);
        }, 300);
      }
      else{
        clearTimeout(dialogTimeOut);
        dialogTimeOut = setTimeout(() => {
          closeDialog();
        }, 3000);
      }
    },
    [archivoCargado]);


    return (
        <div className="cargarExcel">
            <div className="label">
                Selecciona el botón de buscar archivo y elige el archivo excel que deseas subir, a continuación da clic en el botón "Subir excel" para cargar el archivo a la base de datos.
            </div>
            <div className="upload-content">
            <
            TextField id = "nombreArchivo"
            variant = "outlined"
            size = "small"
            className = "input"
            value= {nameFile}/ >

                  <input
                    hidden
                    id="contained-button-file"
                    multiple
                    type="file"
                    onChange={(e) => onSelectFile(e)}
                  />
                  <label htmlFor="contained-button-file">
                    <Button
                    variant="contained"
                    color="primary"
                    component="span"
                    className = "button">
                      Buscar archivo
                    </Button>
                  </label>


                  <
                    span className = "space-buttons" > < /span> <
                    Button variant = "contained"
                    style = {
                      {
                        backgroundColor: '#4CAF50',
                        color: '#FFFFFF'
                      }
                    }
                    onClick = {
                      () => {
                        onCargar();
                      }
                    } >
                    <
                    SvgIcon component = {
                      Excel
                    }
                    /> <
                    span className = "space" > < /span>
                    Cargar Excel <
                    /Button>
            </div>
        </div>
    )
}

export default CargarExcel
