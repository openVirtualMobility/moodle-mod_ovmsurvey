import axios from 'axios'

export const RECEIVE_STEP = 'RECEIVE_STEP'
export const CHANGE_STEP_REQUEST = 'CHANGE_STEP_REQUEST'
export const CHANGE_STEP = 'CHANGE_STEP'
export const CHANGE_NEXT_STEP = 'CHANGE_NEXT_STEP'
export const CHANGE_PREV_STEP = 'CHANGE_PREV_STEP'
export const LOAD_RESPONSES = 'LOAD_RESPONSES'
export const RECEIVE_QUESTIONS = 'RECEIVE_QUESTIONS'
export const RECEIVE_RESPONSES = 'RECEIVE_RESPONSES'
export const ADD_RESPONSE = 'ADD_RESPONSE'
export const SET_STATUS = 'SET_STATUS'
export const CHECK_STATUS = 'CHECK_STATUS'
export const REMOVE_STATUS = 'REMOVE_STATUS'

const lang = moodle_lang != undefined ? moodle_lang : "en";

export const requestChangeStep = (creds) => dispatch => {
    dispatch({
        type: CHANGE_STEP_REQUEST,
        creds
    })
}

export const changeStep = (creds, survey, step) => dispatch => {
    if (creds.action == 'prev') {
        dispatch({
            type: CHANGE_PREV_STEP,
            survey: survey,
            creds
        })
    } else {
        dispatch({
            type: CHANGE_NEXT_STEP,
            survey: survey,
            creds
        })
    }
}

export const loadQuestions = (survey_id, status) => dispatch => {
    let uri = `json/questions_student_${lang}.json`;
    if (status && status == "teacher") {
        uri = `json/questions_teacher_${lang}.json`;
    }

    axios.get('actions.php/skill/' + survey_id)
        .then((res) => {
            let skill = res.data.results;
            if (skill.length > 0) {
                fetch(uri)
                    .then(function(response) {
                        return response.json()
                    })
                    .then(function(json) {
                        dispatch({
                            type: RECEIVE_STEP,
                            payload: parseInt(skill)
                        })
                        dispatch({ 
                            type: RECEIVE_QUESTIONS, 
                            payload: { json: json[lang] }
                        })
                    })

                axios.get('actions.php/responses/' + survey_id + '/' + skill)
                    .then((res) => {
                        dispatch({ 
                            type: RECEIVE_RESPONSES, 
                            payload: res.data.results 
                        })
                    })
            } else {
                fetch(uri)
                    .then(function(response) {
                        return response.json()
                    })
                    .then(function(json) {
                        dispatch({ 
                            type: RECEIVE_QUESTIONS, 
                            payload: { json: json[lang] }
                        })
                    })
            }
        })
}

export const loadResponses = (survey_id, step) => dispatch => {
    dispatch({ 
        type: CHANGE_STEP_REQUEST, 
        payload: null
    })
    axios.get('actions.php/responses/' + survey_id + '/' + step)
        .then((res) => {
            dispatch({ 
                type: RECEIVE_RESPONSES, 
                payload: res.data.results 
            })
        })
}

export const saveUserResponse = (survey_id, survey_type, step, question, response) => dispatch => {
    axios.post('actions.php/response', {
        survey_id: survey_id,
        survey_type: survey_type,
        step_id: step,
        question_id: question,
        response: response,
    })
    .then(function (res) {
        dispatch({ 
            type: ADD_RESPONSE, 
            payload: res.data.results
        })
    })
}

export const setStatus = (status) => dispatch => {
    dispatch({ type: SET_STATUS, payload: status })
}

export const checkStatus = () => dispatch => {
    dispatch({ type: CHECK_STATUS })
}

export const removeStatus = (survey_id, step) => dispatch => {
    axios.get('actions.php/change_status/' + survey_id + '/' + step)
        .then((res) => {
            dispatch({ type: REMOVE_STATUS })
        })
}