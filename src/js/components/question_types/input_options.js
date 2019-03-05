import React, { Component } from 'react'
import {translations} from '../../../lang/translations.js'
const lang = moodle_lang.length > 0 ? moodle_lang : "en"

export default class InputOptions extends Component {
    constructor() {
        super()
        this.state = {
            selectedResponse: 0
        }
    }

    createButtonItem = (val) => {
        return(
            <button 
                onClick={ () => this._handleSelection(parseInt(val)) } 
                aria-label={ `${translations[lang]['choice_label']} ${val}` }
                className={ parseInt(this.state.selectedResponse) == parseInt(val) ? "ovm-option active" : "ovm-option" }>
                { val }
            </button>
        )
    }

    componentWillReceiveProps(newProps) {
        if (typeof newProps.response !== 'undefined' && typeof newProps.response !== undefined) {
            this.setState({ selectedResponse: newProps.response })
        }
    }

    _handleSelection = (index) => {
        let self = this
        new Promise(function(resolve, reject) {
            setTimeout(() => resolve(
                self.setState({ selectedResponse: index })
            ), 1);
        }).then(function(result) {
            self.props.selectedValues(index)
        })
    }

    _showTip = () => {

    }

    render() {
        return(
            <div className="ovm-option-list">
                <div className="tooltips">
                    <i className="fa fa-question-circle"></i>
                    <span className="tooltips-text">
                        { translations[lang]['scale_info_1'] }<br />
                        { translations[lang]['scale_info_4'] }
                    </span>
                </div> 
                { this.createButtonItem(1) }
                { this.createButtonItem(2) }
                { this.createButtonItem(3) }
                { this.createButtonItem(4) }
            </div>
        )
    }
}