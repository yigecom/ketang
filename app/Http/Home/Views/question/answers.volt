{{ partial('macros/answer') }}

{% if pager.total_pages > 0 %}
    {% for item in pager.items %}
        {{ answer_card(item,auth_user) }}
    {% endfor %}
    {{ partial('partials/pager_ajax') }}
{% endif %}