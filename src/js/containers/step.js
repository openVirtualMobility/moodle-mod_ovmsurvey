import React, { Component, Fragment } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

import QuestionStep from './question_step'
import { translations } from '../../lang/translations.js'
const lang = moodle_lang.length > 0 ? moodle_lang : "en"

class Step extends Component {
    constructor() {
        super()
        this.state = {
            disabled: true,
            showHint: false,
            progressVal: 0
        }
        this.showHint = this.showHint.bind(this)
    }
    
    componentWillReceiveProps(newProps) {
        if (newProps.step > 0) {
            this.setState({ disabled: false })
        } else {
            this.setState({ disabled: true })
        }

        if (newProps.responses.length > 0) {
            this.progressVal(newProps.responses.length);
        }
    }

    scrollToTop() {
        let timeOut;
        if (document.body.scrollTop!=0 || document.documentElement.scrollTop!=0) {
            window.scrollBy(0, -50);
            timeOut=setTimeout(this.scrollToTop(), 1000);
        }
        else clearTimeout(timeOut);
    }

    canGoNext = () => {
        if ( this.props.responses.length >= this.props.questionsCount ) {
            return true;
        }
        return false;
    }

    showHint(event) {
        if (this.props.responses.length < this.props.questionsCount) {
            this.setState({ showHint: !this.state.showHint })
        }
    }

    progressVal(responses) {
        let val = 0;
        if (responses && this.props.questionsCount) {
            val = parseFloat(100 * (parseInt(responses) / parseInt(this.props.questionsCount)));
        }
        this.setState({ progressVal: val })
    }

    render() {
        return (
            <React.Fragment>
                <div className="container">
                    <div className="row">
                        <div className="col-12">
                            <progress   value={this.state.progressVal} 
                                        max="100" 
                                        className={ this.state.progressVal > 0 ? "ovmsurvey-progress" : "ovmsurvey-progress hidden"}>
                                {this.state.progressVal + "%"}
                            </progress>
                        
                        </div>
                        <QuestionStep />
                    </div>
                    <div className="row mt-5">
                        { this.state.progressVal >= 99
                            ?   <div>
                                    <h4>{ translations[lang]['end'] }</h4>
                                    <p>
                                        <a href={`view.php?id=${this.props.survey_id}&review=1`} className="btn btn-primary">
                                            { translations[lang]['view_report'] }
                                        </a>
                                    </p>
                                </div>
                            :   null
                        }
                    </div>
                </div>
            </React.Fragment>
        )
    }

    handleClick(action) {
        const creds = { action: action }
        this.scrollToTop()
    }
}

Step.propTypes = {
    survey_id: PropTypes.number,
    step: PropTypes.number,
    questionsCount: PropTypes.number,
    responses: PropTypes.array,
    progress: PropTypes.number,
}

function mapStateToProps(state) {
    return {
        survey_id: state.survey.id,
        step: state.step.step,
        questionsCount: state.step.questionsCount,
        responses: state.responses.values,
        progress: state.step.progress,
    }
}
    
export default connect(mapStateToProps)(Step)