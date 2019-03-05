import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { loadResponses, loadQuestions, setStatus, checkStatus, removeStatus } from '../actions'

import {translations} from '../../lang/translations.js'
const lang = moodle_lang.length > 0 ? moodle_lang : "en"

import Step from './step'

class App extends Component {
    constructor(props) {
        super(props)
        const {dispatch} = props
        this.boundActionCreators = bindActionCreators(loadResponses, dispatch)
        this.boundActionCreators = bindActionCreators(loadQuestions, dispatch)
        this.boundActionCreators = bindActionCreators(setStatus, dispatch)
        this.boundActionCreators = bindActionCreators(checkStatus, dispatch)
        this.boundActionCreators = bindActionCreators(removeStatus, dispatch)
    }

    componentWillMount() {
        this.props.dispatch(checkStatus());
    }

    _setStatus = (status) => {
        this.props.dispatch(setStatus(status));
    }

    _removeStatus = () => {
        this.props.dispatch(removeStatus(this.props.survey, this.props.step));
        this.props.dispatch(loadQuestions());
    }

    render() {
        const status = this.props.status;
        return (
            <div className="container">
                <div className="row">
                    <div id="fadein" className="col-md-12 fadein">
                        { !status
                            ?   <div className="col-md-12">
                                    <h3>{ translations[lang]['set_status'] }</h3>
                                    <button onClick={() => this._setStatus("student")} className="btn btn-secondary mr-2">
                                        { translations[lang]['status_student'] }
                                    </button>
                                    <button onClick={() => this._setStatus("teacher")} className="btn btn-secondary">
                                        { translations[lang]['status_teacher'] }
                                    </button>
                                </div>
                            :   <div className="col-md-12">
                                    <h3>{ translations[lang]['my_status'] } 
                                        <button onClick={() => this._removeStatus()} className="btn btn-secondary">
                                            { translations[lang][this.props.status] }
                                        </button>
                                    </h3>
                                    <Step />
                                </div>
                        }
                    </div>
                </div>
            </div>
        )
    }
}

App.propTypes = {
    dispatch: PropTypes.func.isRequired,
    survey: PropTypes.number,
    step: PropTypes.number,
    status: PropTypes.string
}

function mapStateToProps(state) {
    return {
        survey: state.survey.id,
        step: state.step.step,
        status: state.step.status
    }
}
  
export default connect(mapStateToProps)(App)