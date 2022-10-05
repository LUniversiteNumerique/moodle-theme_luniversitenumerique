define(['jquery'], function($) {
    return {
        init: function() {
            const oneSectionPerPage = $('.section.section-summary').length > 0 ? true : false;
            if (!oneSectionPerPage) {
                const isPunchyCourse = $('#punchy-infos-wrapper').length > 0 ? true : false;
                if (isPunchyCourse) {
                    $sectionNames = $('li.section').find('.sectionname').hide();
                }
            }
        }
    };
});
