jQuery(document).ready(function($) {
    var button = $('<button/>', {
        text: 'Update Additional Labels from Singular/Plural Label',
        type: 'button',
        id: 'update-labels-button',
        class: 'button button-primary',
        click: function() {
            var singularLabel = $('#singular_label').val().trim();
            var pluralLabel = $('#plural_label').val().trim(); // Ensure you have a field for plural labels.

            if(singularLabel && pluralLabel) {
                $.each(oceanCPTLabels.labelsToUpdate, function(key, selector) {
                    var $field = $(selector);
                    if ($field.length) {
                        var currentVal = $field.val();
                        if (typeof currentVal === 'string') {
                            if (key === 'menu_name') {
                                // Directly replace the value with pluralLabel for menu_name
                                $field.val(pluralLabel);
                            } else if (['all_items', 'view_items', 'search_items', 'popular_items', 'items_list', 'filter_items_list', 'items_list_navigation'].includes(key)) {
                                var regex = new RegExp('Item(s)?', 'gi');
                                $field.val(currentVal.replace(regex, function(match) {
                                    // Determine if the match is plural ('Items') and adjust accordingly
                                    return match.toLowerCase() === 'items' ? pluralLabel : singularLabel;
                                }));
                            } else {
                                // For singular fields, simply replace 'Item' with singularLabel
                                $field.val(currentVal.replace(/Item/i, singularLabel));
                            }
                        }
                    }
                });
            } else {
                alert('Please enter both a singular and a plural label.');
            }
        }
    });

    // Append the button to a specific location in the form
    $('#ocpt_panel_basic_settings table').after(button);

    // Confirmation dialog for Delete Post Type button
    $('#delete-cpt-button').click(function(event) {
        var confirmDeletion = confirm('Are you sure you want to delete this Custom Post Type? This action cannot be undone.');
        if (!confirmDeletion) {
            event.preventDefault();
        }
    });
    // Confirmation dialog for Delete Taxonomy button
    $('#delete-taxonomy-button').click(function(event) {
        var confirmDeletion = confirm('Are you sure you want to delete this Taxonomy? This action cannot be undone.');
        if (!confirmDeletion) {
            event.preventDefault();
        }
    });
});