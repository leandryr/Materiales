import React from "react";
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import Header from '../../components/Header/Header';
import { makeStyles } from '@material-ui/core/styles';
import  Add  from '../../img/add.js';
import  Beneficiario  from '../../img/beneficiario.js';
import  Reportes  from '../../img/reportes.js';
import  Reclamo  from '../../img/reclamo.js';


import  MiCuenta  from '../../img/mi_cuenta.js';
import  Logout  from '../../img/logout.js';
import SvgIcon from "@material-ui/core/SvgIcon";
import { withRouter } from 'react-router-dom';
import './Sidebar.scss'

const useStyles = makeStyles((theme) => ({
    root: {
        width: '100%',
        maxWidth: 360,
        color: theme.palette.common.white,
        marginTop: 44
    },
    list: {
        width: '90%',
        marginBottom: 18,
        marginLeft: 20
    }
}));

const Sidebar = props => {
    const { history, credentials, onLogOut } = props;
    const defaultPath = '/home/';
    const classes = useStyles();
    const handleListItemClick = (url) => {
        history.push(`${defaultPath}${url}`);
    };

    const handleClose = () => {
      // LogOut
        onLogOut();
    };

    return (
        <div className={classes.root}>
            <Header
              credentials = {credentials}
              onLogOut = {onLogOut}
            />
            <List component="nav" aria-label="sidebar">
                <ListItem button className={classes.list} onClick={() => handleListItemClick('historial')}>
                    <ListItemIcon>
                        <SvgIcon component={MiCuenta} viewBox="0 0 30 30"/>
                    </ListItemIcon>
                    <ListItemText primary="Historial de busqueda" />
                </ListItem>

                {(credentials.rol === 'ROLE_ADMINISTRADOR' || credentials.rol === 'ROLE_CAPTURISTA') ? (
                  <ListItem button className={classes.list} onClick={() => handleListItemClick('nuevo_registro')}>
                      <ListItemIcon>
                          <SvgIcon component={Add} viewBox="0 0 30 30"/>
                      </ListItemIcon>
                      <ListItemText primary="Nuevo registro" />
                  </ListItem>
                              ):
                              ('')}



                {(credentials.rol === 'ROLE_ADMINISTRADOR') ? (
                  <ListItem button className={classes.list} onClick={() => handleListItemClick('usuarios')}>
                                    <ListItemIcon>
                                        <SvgIcon component={Beneficiario} viewBox="0 0 30 30"/>
                                    </ListItemIcon>
                                    <ListItemText primary="Usuarios" />
                                </ListItem>
                              ):
                              ('')}

                <ListItem button className={classes.list} onClick={() => handleListItemClick('reportes')}>
                    <ListItemIcon>
                        <SvgIcon component={Reportes} viewBox="0 0 30 30"/>
                    </ListItemIcon>
                    <ListItemText primary="Reportes" />
                </ListItem>

                {(credentials.rol === 'ROLE_ADMINISTRADOR') ? (
                  <ListItem button className={classes.list} onClick={() => handleListItemClick('reclamo')}>
                      <ListItemIcon>
                          <SvgIcon component={Reclamo} viewBox="0 0 30 30"/>
                      </ListItemIcon>
                      <ListItemText primary="Nuevo Reclamo" />
                  </ListItem>
                              ):
                              ('')}


                <ListItem button className={classes.list} onClick={() => handleClose()}>
                    <ListItemIcon>
                        <SvgIcon component={Logout} viewBox="0 0 30 30"/>
                    </ListItemIcon>
                    <ListItemText primary="Cerrar Sesión" />
                </ListItem>
            </List>
        </div>
    );
}

export default withRouter(Sidebar);
