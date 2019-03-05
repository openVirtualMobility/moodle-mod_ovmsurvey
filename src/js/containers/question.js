import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { questionAnswered, saveUserResponse } from '../actions'

import InputOptions from '../components/question_types/input_options'

class Question extends Component {
    constructor(props) {
        super(props)
        this.state = {
            response: 0
        }
    }

    componentDidMount() {
        if (typeof(this.props.responses) != 'undefined') {
            let responsesArray = Object.values(this.props.responses)
            let qResponses = responsesArray.filter((i, _) => i["question_id"] == this.props.item.id)
            if (qResponses[0]) {
                this.setState({ response: qResponses[0].response })
            } else {
                this.setState({ response: 0 })
            }
        }
    }

    componentWillReceiveProps(newProps) {
        if (typeof(newProps.responses) != 'undefined') {
            let responsesArray = Object.values(newProps.responses)
            let qResponses = responsesArray.filter((i, _) => i["question_id"] == this.props.item.id)
            if (qResponses[0]) {
                this.setState({ response: qResponses[0].response })
            } else {
                this.setState({ response: 0 })
            }
        }
    }

    _handleSelectedValues = (response) => {
        this.props.saveUserResponse(this.props.survey_id, this.props.survey_type, this.props.step, this.props.item.id, response)
    }

    render() {
        return (
            <div className={ this.state.response != 0
                ? "col-12 ovmsurvey-question-row checked"
                : "col-12 ovmsurvey-question-row"
            }>
                { this.state.response != 0
                    ?   <svg version="1.1" className="check-svg" xmlns="http://www.w3.org/2000/svg" 
                            viewBox="0 0 514 515.3" preserveAspectRatio='xMinYMin'>
                            <g>
                                <path d="M513,257.9c0-141.4-114.6-256-256-256S1,116.5,1,257.9s114.6,256,256,256S513,399.3,513,257.9L513,257.9z M452.7,158.2
                                    l-241,241l0,0l-17.2,17.2L61.3,283.2l58.8-58.8l74.5,74.5L394,99.5L452.7,158.2L452.7,158.2z"/>
                                <polygon points="119.1,222.5 60.3,281.3 193.5,414.5 451.7,156.3 393,97.6 193.6,297 "/>
                            </g>
                        </svg>
                    : null
                }
                { this.props.item.type == 'file' && this.props.survey == 'initial' 
                    ?   null
                    :    <div className={ `ovmsurvey-question ${this.props.item.type}` }>
                            <div className="row">
                                <div className="col-12 col-md-9 col-sm-12 ovmsurvey-statement">
                                    {this.props.item.stmt}
                                </div>
                                <div className="col-12 col-md-3 col-sm-12">
                                    {
                                        this.props.item.type == 'choice'
                                        ?   <InputOptions 
                                                selectedValues={this._handleSelectedValues}
                                                qid={this.props.item.id}
                                                response={this.state.response}
                                            />
                                        :   null
                                    }
                                </div>
                            </div>
                        </div>
                }
            </div>
        )
    }
}

Question.propTypes = {
    survey_id: PropTypes.number,
    survey_type: PropTypes.string,
    step: PropTypes.number,
    responses: PropTypes.array,
}

function mapStateToProps(state) {
    return {
        survey_id: state.survey.id,
        survey_type: state.survey.type,
        step: state.step.step,
        responses: state.responses.values,
    }
}

function mapDispatchToProps(dispatch) {
    return bindActionCreators({ questionAnswered, saveUserResponse }, dispatch)
}
    
export default connect(mapStateToProps, mapDispatchToProps)(Question)