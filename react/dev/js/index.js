import 'babel-polyfill';
import React from 'react';
import ReactDOM from "react-dom";
var index = document.getElementById('firstPage');
var scndpage = document.getElementById('secondPage');
if(index!=null){
    ReactDOM.render(
        <h1>this is index</h1>,
        index
    );
}
if(scndpage!=null){
    ReactDOM.render(
        <h1>this is 2nd</h1>,
        scndpage
    );
}

