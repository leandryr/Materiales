import React from "react";
import { render } from "react-dom";
import { BrowserRouter as Router } from "react-router-dom";
import App from "./components/App";
import "./index.scss";
import { createMuiTheme, ThemeProvider } from '@material-ui/core/styles';
import { esES } from '@material-ui/core/locale';

const theme = createMuiTheme({
  palette: {
    primary: {
      main: '#192E47',
    },
    secondary: {
      main: '#346298',
    }
  },
}, esES);

render(
    <Router>
      <ThemeProvider theme={theme}>
        <App />
      </ThemeProvider>
    </Router>,
  document.getElementById("root")
);