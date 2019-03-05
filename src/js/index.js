import React from 'react';
import { render } from 'react-dom';
import { createStore, applyMiddleware } from 'redux';
import { Provider } from 'react-redux';
import thunkMiddleware from 'redux-thunk';
import surveyApp from './reducers';
import App from './containers/app';
require('./components/utils.js');
require('../main.scss');

let createStoreWithMiddleware = applyMiddleware(thunkMiddleware)(createStore);
let store = createStoreWithMiddleware(surveyApp);

let rootElement = document.getElementById('surveyapp');

render(
  <Provider store={store}>
    <App />
  </Provider>,
  rootElement
);