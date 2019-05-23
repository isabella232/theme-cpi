import $ from 'jquery';

class MemberForm {
    constructor() {
        this.$dollarInput = $('.member-form input#dollar_amount');
        this.$radioInputs = $('.member-form__radio');
        this.$noteContainer = $('.member-form__radio-help');
        this.init();
    }
    init() {
        // Update on load and when radios or number input changes
        let that = this;
        that.updateHelpText();
        this.$dollarInput.on('change keyup paste', function() {
            that.updateHelpText();
        });
        this.$radioInputs.change(function() {
            that.updateHelpText();
        });
    }
    updateHelpText() {
        let level = this.computeLevel();

        let updated_text = level
            ? `<p>This donation will make you ${level} member.</p>`
            : '<p><strong>Donate $35 or more</strong> and receive special members-only updates, behind-the-scenes Q&A with journalists, and more.</p>';
        this.$noteContainer.html(updated_text);
    }
    computeLevel() {
        let dollarAmount = parseInt($('.member-form input#dollar_amount').val());
        let frequency = $('.member-form__radio:checked').val();
        let multiplier = frequency == 'monthly' ? 12 : 1;
        let annualAmount = dollarAmount * multiplier;

        let level =
            annualAmount >= 1e3
                ? 'an <strong>Investigator</strong>'
                : annualAmount >= 500
                    ? 'a <strong>Truth Teller</strong>'
                    : annualAmount >= 35
                        ? 'a <strong>Watchdog</strong>'
                        : null;

        return level;
    }
}

export default MemberForm;
