define(['jquery', 'core/ajax', 'core/notification'],
function($, ajax, notification) {

    var GetAnswers = function(selector, surveyId, total) {
        this._region = $(selector);
        this._surveyid = surveyId;
        this._totalStmts = total;

        this._setUserChoices();
    };

    GetAnswers.prototype._setUserChoices = function() {
        ajax.call([{
            methodname: 'mod_ovmsurvey_get_answers',
            args: {
                surveyid: this._surveyid
            },
            done: function(data) {
                this.parseData(data);

                if (this._totalStmts == data.length) {
                    this.showReviewButton();
                }

                return true;
            }.bind(this),
            fail: notification.exception
        }]);
    };

    GetAnswers.prototype.parseData = function(data) {
        var len = data.length;
        for(var i = 0; i < len; i++) {
            var qid = data[i]['question_id'];
            var rid = data[i]['response'];
            var elem = $(this._region).find('.ovm-option[data-id="'+qid+'"][data-value="'+rid+'"]');
            elem.addClass('active');
            elem.parent().parent().parent().parent().parent().find('.check-svg').removeClass('hidden');
        }
    };

    GetAnswers.prototype.showReviewButton = function() {
        return $('#ovmsurvey-review-button').show();
    }

    return GetAnswers;
});