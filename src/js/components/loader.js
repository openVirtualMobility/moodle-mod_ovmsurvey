import React, { Component } from 'react'

export default class Loader extends Component {
    render() {
        return(
            <svg width="100%" 
                height="40px" 
                xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 0 100 100" 
                preserveAspectRatio="xMidYMid"
                className="ovmsurvey-loader">
                <circle cx="50" cy="50" fill="none"
                    strokeWidth="10" r="35" 
                    strokeDasharray="164.93361431346415 56.97787143782138">
                    <animateTransform attributeName="transform" 
                        type="rotate" 
                        calcMode="linear" 
                        values="0 50 50;360 50 50" 
                        keyTimes="0;1" 
                        dur="1s" 
                        begin="0s" 
                        repeatCount="indefinite">
                    </animateTransform>
                </circle>
            </svg>
        )
    }
}