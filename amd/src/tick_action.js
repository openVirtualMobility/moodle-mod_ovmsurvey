define(['jquery', 'core/ajax', 'core/notification'],
function($, ajax, notification) {

    var TickAction = function(selector, surveyId, total) {
        this._region = $(selector);
        this._surveyid = surveyId;
        this._totalStmts = total;

        this._region.find('.ovm-option-list').unbind().on('click', 'button', this._setUserChoice.bind(this));
    };

    TickAction.prototype._setUserChoice = function(element) {
        var elem = $(element.target);
        var stmtid = elem.data('id');
        var value = elem.data('value');
        var status = localStorage.getItem('ovms-status');

        if (stmtid != "" && value != "") {
            ajax.call([{
                methodname: 'mod_ovmsurvey_set_answer',
                args: {
                    surveyid: this._surveyid,
                    status: status ? status : '',
                    stmtid: stmtid,
                    value: value
                },
                done: function(data) {
                    $(element.target).parent().find('button').removeClass('active');
                    $(element.target).addClass('active');
                    $(element.target).parent().parent().parent().parent().parent().find('.check-svg').removeClass('hidden');

                    if (this._totalStmts == data) {
                        this.showReviewButton();
                    }

                    return true;
                }.bind(this),
                fail: notification.exception
            }]);
        }
    };

    TickAction.prototype.showReviewButton = function() {
        return $('#ovmsurvey-review').show();
    };

    return TickAction;
});