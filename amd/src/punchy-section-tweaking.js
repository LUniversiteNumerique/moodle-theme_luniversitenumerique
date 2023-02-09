define(['jquery'], function ($) {
    return {
        init: function () {
            const oneSectionPerPage = $('.section.section-summary').length > 0 ? true : false;
            if (!oneSectionPerPage) {
                const isPunchyCourse = $('#punchy-infos-wrapper').length > 0 ? true : false;
                if (isPunchyCourse) {
                    $sectionNames = $('li.section').find('.sectionname');
                    $('li.section').each(function () {
                        if ($(this).has('.h5p-placeholder').length > 0) {
                            $(this).find('.sectionname').hide();
                        }
                    });
                }
            }
        }
    };
});
