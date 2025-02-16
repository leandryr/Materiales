import React from 'react';
import { makeStyles } from '@material-ui/core/styles';
import CircularProgress from '@material-ui/core/CircularProgress';
import { green } from '@material-ui/core/colors';

const useStyles = makeStyles((theme) => ({
  root: {
    display: 'flex',
   '& > * + *': {
     marginLeft: theme.spacing(2),
   },

  },

  fabProgress: {
    color: green[500],
  },

}));

export default function Loader() {
  const classes = useStyles();

  return (
    <div className={classes.root}>

      <CircularProgress size={100} className={classes.fabProgress} />

    </div>
  );
}
