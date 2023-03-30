<script type="text/javascript">
    jQuery(document).ready(function($) {
        var series_selector = $('#cricket_series').select2({
            placeholder: {
                id: '-1',
                text: 'Select a series'
            },
            ajax: {
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        action: 'cricket_series_list'
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                                slug: item.name,
                                startDate: item.startDate,
                                endDate: item.endDate,
                                matches: item.matches
                            }
                        })
                    };
                },
                cache: true
            },
            templateResult: function(state) {
                if (!state.id) {
                    return state.text;
                }

                const text = state.text;
                const matches = state.matches;
                const endDate = state.endDate;
                const startDate = new Date(state.startDate);
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                var $state = $(
                    '<div><strong>' + text + '</strong></div><div>' + matches + ' matches</div><div>' + monthNames[startDate.getMonth()] + " " + startDate.getDate() + ' - ' + endDate + " " + startDate.getFullYear() +
                    '</div>'
                );

                return $state;
            }
        });
    });
</script>