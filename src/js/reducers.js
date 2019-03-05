import _ from 'lodash'
import { combineReducers } from 'redux'
import {
    RECEIVE_STEP, CHANGE_STEP_REQUEST, CHANGE_NEXT_STEP, CHANGE_PREV_STEP, 
    RECEIVE_QUESTIONS, RECEIVE_RESPONSES, ADD_RESPONSE, SET_STATUS, 
    CHECK_STATUS, REMOVE_STATUS
} from './actions'

const windowUrl = new URL(window.location.href);
var surveyId = windowUrl.searchParams.get("id");

// Survey reducer
function survey(state = {
        id: 0,
        type: 'initial'
    }, action) {
        const cond = surveyId.length > 0;
        switch (cond) {
            case cond:
                return { ...state, id: parseInt(surveyId) }
            default:
                return state
        }
}

// Step reducer
function step(state = {
        step: 0,
        stepName: '',
        progress: 0,
        questions: [],
        questionsCount: 0,
        status: localStorage.getItem('ovms-status')
    }, action) {
        switch (action.type) {

            case RECEIVE_STEP:
                return { ...state,
                    step: action.payload
                }

            case CHANGE_STEP_REQUEST:
                return { ...state,
                    step: state.step,
                    stepName: '',
                    questions: [],
                    fileTypeQuestions: 0
                }

            case RECEIVE_QUESTIONS:
                let qs = Object.values(action.payload.json[0])[state.step]
                let count = 0;
                if (qs != undefined && qs['subskills'] != undefined) {
                    qs['subskills'].map(s => {
                        count = count + s.statements.length;
                    });
                }
                return { ...state,
                    stepName: qs != undefined ? qs['name'] : null,
                    progress: parseFloat(100 * (parseInt(state.step) / Object.values(action.payload.json[0]).length)),
                    questions: qs != undefined ? qs['subskills'] : null,
                    questionsCount: count
                }

            case CHANGE_NEXT_STEP:
                let nextStep = state.step+1
                return { ...state,
                    step: nextStep,
                    progress: parseFloat(100 * (parseInt(nextStep) / state.questions.length)),
                }

            case CHANGE_PREV_STEP:
                let prevStep = state.step-1
                return { ...state,
                    step: prevStep,
                    progress: prevStep > 0 ? parseFloat(100 * (parseInt(prevStep) / state.questions.length)) : 0,
                }

            case SET_STATUS:
                localStorage.setItem('ovms-status', action.payload);
                return { ...state, status: action.payload }

            case CHECK_STATUS:
                let localAnswer = localStorage.getItem('ovms-status');
                return { ...state, status: localAnswer ? localAnswer : null }
        
            case REMOVE_STATUS:
                localStorage.removeItem('ovms-status');
                return { ...state, status: null, stepName: '', questions: [] }
                
            default:
                return state
        }
}

// Responses reducer
function responses(state = {
    values: []
}, action) {
    switch (action.type) {
        case CHANGE_STEP_REQUEST:
            return { ...state,
                values: []
            }

        case RECEIVE_RESPONSES:
            return { ...state,
                values: action.payload != null ? Object.values(action.payload) : []
            }

        case ADD_RESPONSE:
            const rArray = state.values
            let found = false
            rArray.filter((item, pos, arr) => {
                if (item.question_id == action.payload.question_id) {
                    found = true
                    item.response = action.payload.response
                }
            })
            return { ...state,
                values: found == true ? rArray : state.values.concat(action.payload)
            }

        default:
            return state
    }
}

// Combined reducers
const surveyApp = combineReducers({
    survey,
    step,
    responses,
})

export default surveyApp