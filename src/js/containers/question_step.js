import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { loadQuestions, loadResponses } from '../actions'

import Question from './question'

class QuestionStep extends Component {
    constructor(props) {
        super(props)
        const {dispatch} = props
        this.boundActionCreators = bindActionCreators(loadQuestions, dispatch)
        this.boundActionCreators = bindActionCreators(loadResponses, dispatch)
    }
        
    componentDidMount = () => {
        let { dispatch } = this.props
        dispatch( loadQuestions(this.props.survey_id, this.props.status) )
    }

    renderSubskill = (subskill) => {
        const questions = subskill.statements.map((question, i) => {
            return(
                <Question 
                    key={i}
                    item={question} 
                    refreshAnswers={this._handleAnswers} 
                    responses={this.props.responses}
                />
            )
        });
        return (
            <div key={subskill.name}>
                <h4 className="ovmsurvey-subcompetency">{subskill.name}</h4>
                <div>{questions}</div>
            </div>
        );
    }

    render() {
        return (
            <div className="col-12">
                <h3 className="ovmsurvey-competency">{ this.props.stepName }</h3>
                { this.props.questions.map(subskill => {
                    return this.renderSubskill(subskill);
                })}
            </div>
        )
    }
}

QuestionStep.propTypes = {
    survey_id: PropTypes.number,
    survey_type: PropTypes.string,
    step: PropTypes.number,
    stepName: PropTypes.string,
    questions: PropTypes.array,
    responses: PropTypes.array,
    status: PropTypes.string
}

function mapStateToProps(state) {
    return {
        survey_id: state.survey.id,
        survey_type: state.survey.type,
        step: state.step.step,
        stepName: state.step.stepName,
        questions: state.step.questions,
        responses: state.responses.values,
        status: state.step.status
    }
}

export default connect(mapStateToProps)(QuestionStep)