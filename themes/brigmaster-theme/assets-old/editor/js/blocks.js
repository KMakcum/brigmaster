(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var MediaUpload = wp.blockEditor.MediaUpload;
  var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var TextControl = wp.components.TextControl;
  var TextareaControl = wp.components.TextareaControl;
  var SelectControl = wp.components.SelectControl;
  var Button = wp.components.Button;
  var useSelect = wp.data.useSelect;

  function ConstructlyMediaControl(props) {
    var value = props.value && Number(props.value) > 0 ? Number(props.value) : 0;
    var attachment = useSelect(
      function (select) {
        if (!value) {
          return null;
        }
        return select('core').getMedia(value);
      },
      [value]
    );
    var onChange = props.onChange;

    return el(
      MediaUploadCheck,
      {},
      el(MediaUpload, {
        allowedTypes: ['image'],
        value: value || undefined,
        onSelect: function (media) {
          onChange(media && media.id ? Number(media.id) : 0);
        },
        render: function (ref) {
          var open = ref.open;
          return el('div', { className: 'bm-editor-media-field' }, [
            el('span', { className: 'bm-editor-block__label', key: 'lab' }, props.label || ''),
            attachment && attachment.source_url
              ? el('img', {
                  key: 'img',
                  src: attachment.source_url,
                  alt: '',
                  style: { maxWidth: '160px', height: 'auto', display: 'block', marginBottom: '8px', borderRadius: '4px' }
                })
              : null,
            el(
              'div',
              { key: 'btns', style: { display: 'flex', gap: '8px', flexWrap: 'wrap' } },
              [
                el(Button, { variant: 'secondary', onClick: open }, value ? 'Заменить изображение' : 'Выбрать из медиатеки'),
                value
                  ? el(Button, { variant: 'link', isDestructive: true, onClick: function () { onChange(0); } }, 'Удалить')
                  : null
              ]
            )
          ]);
        }
      })
    );
  }

  function clone(value) {
    return JSON.parse(JSON.stringify(value));
  }

  function renderField(field, value, onChange) {
    if (field.type === 'textarea') {
      return el(TextareaControl, {
        label: field.label,
        value: value || '',
        rows: field.rows || 3,
        onChange: onChange
      });
    }

    if (field.type === 'select') {
      return el(SelectControl, {
        label: field.label,
        value: value || '',
        options: field.options || [],
        onChange: onChange
      });
    }

    return el(TextControl, {
      label: field.label,
      value: value || '',
      type: field.type || 'text',
      onChange: onChange
    });
  }

  function renderRepeaterCards(field, items, updateItem) {
    return el(
      'div',
      { className: 'bm-editor-grid ' + (field.columnsClass || '') },
      (items || []).map(function (item, index) {
        return el(
          'div',
          { className: 'bm-editor-card', key: field.name + '-' + index },
          [
            el('h4', { key: 'heading-' + index }, (field.itemLabel || 'Item') + ' ' + (index + 1)),
            (field.fields || []).map(function (subField) {
              if (subField.type === 'media') {
                return el(ConstructlyMediaControl, {
                  key: subField.name + '-' + index,
                  label: subField.label,
                  value: item[subField.name],
                  onChange: function (nextValue) {
                    updateItem(index, subField.name, nextValue);
                  }
                });
              }
              return el(
                Fragment,
                { key: subField.name + '-' + index },
                renderField(subField, item[subField.name], function (nextValue) {
                  updateItem(index, subField.name, nextValue);
                })
              );
            })
          ]
        );
      })
    );
  }

  function renderStringList(field, items, updateItem) {
    return el(
      'div',
      { className: 'bm-editor-grid' },
      (items || []).map(function (item, index) {
        return el(
          Fragment,
          { key: field.name + '-' + index },
          renderField(
            { label: (field.itemLabel || 'Item') + ' ' + (index + 1), type: field.itemType || 'text' },
            item,
            function (nextValue) {
              updateItem(index, nextValue);
            }
          )
        );
      })
    );
  }

  function renderPreview(config, attributes) {
    var header = el(
      'div',
      { className: 'bm-editor-block__header', key: 'header' },
      [
        el('span', { className: 'bm-editor-block__label', key: 'label' }, config.previewLabel),
        el('div', { key: 'title' }, config.preview(attributes))
      ]
    );

    return header;
  }

  function registerSectionBlock(config) {
    registerBlockType(config.name, {
      apiVersion: 3,
      title: config.title,
      icon: config.icon,
      category: 'constructly',
      attributes: config.attributes,
      edit: function (props) {
        var blockProps = useBlockProps({
          className: 'bm-editor-block'
        });

        var attributes = props.attributes;
        var setAttributes = props.setAttributes;

        var fields = config.fields.map(function (field) {
          if (field.type === 'repeater') {
            return el(
              'div',
              { key: field.name },
              [
                el('span', { className: 'bm-editor-block__label', key: 'label' }, field.label),
                renderRepeaterCards(field, attributes[field.name], function (index, key, nextValue) {
                  var nextItems = clone(attributes[field.name] || []);
                  nextItems[index][key] = nextValue;
                  setAttributes((function () {
                    var payload = {};
                    payload[field.name] = nextItems;
                    return payload;
                  })());
                })
              ]
            );
          }

          if (field.type === 'string-list') {
            return el(
              'div',
              { key: field.name },
              [
                el('span', { className: 'bm-editor-block__label', key: 'label' }, field.label),
                renderStringList(field, attributes[field.name], function (index, nextValue) {
                  var nextItems = clone(attributes[field.name] || []);
                  nextItems[index] = nextValue;
                  setAttributes((function () {
                    var payload = {};
                    payload[field.name] = nextItems;
                    return payload;
                  })());
                })
              ]
            );
          }

          return el(
            Fragment,
            { key: field.name },
            renderField(field, attributes[field.name], function (nextValue) {
              var payload = {};
              payload[field.name] = nextValue;
              setAttributes(payload);
            })
          );
        });

        return el(
          'div',
          blockProps,
          [
            renderPreview(config, attributes),
            el('div', { className: 'bm-editor-grid', key: 'fields' }, fields)
          ]
        );
      },
      save: function () {
        return null;
      }
    });
  }

  var heroQuickLinks = [
    { label: 'Фундамент', url: '#calculators' },
    { label: 'Стяжка', url: '#calculators' },
    { label: 'Кирпич', url: '#calculators' }
  ];

  var sectionConfigs = [
    {
      name: 'constructly/home-hero',
      title: 'Constructly Hero',
      icon: 'cover-image',
      previewLabel: 'Hero',
      attributes: {
        title: { type: 'string', default: '' },
        lead: { type: 'string', default: '' },
        primaryLabel: { type: 'string', default: '' },
        primaryUrl: { type: 'string', default: '' },
        secondaryLabel: { type: 'string', default: '' },
        secondaryUrl: { type: 'string', default: '' },
        quickLinksLabel: { type: 'string', default: '' },
        quickLinks: { type: 'array', default: heroQuickLinks },
        themeVariant: { type: 'string', default: 'dark' }
      },
      fields: [
        { name: 'title', label: 'Title' },
        { name: 'lead', label: 'Lead', type: 'textarea', rows: 3 },
        { name: 'primaryLabel', label: 'Primary Button Label' },
        { name: 'primaryUrl', label: 'Primary Button URL', type: 'url' },
        { name: 'secondaryLabel', label: 'Secondary Button Label' },
        { name: 'secondaryUrl', label: 'Secondary Button URL', type: 'url' },
        { name: 'quickLinksLabel', label: 'Quick Links Label' },
        {
          name: 'quickLinks',
          label: 'Quick Links',
          type: 'repeater',
          columnsClass: 'bm-editor-grid--3',
          itemLabel: 'Link',
          fields: [
            { name: 'label', label: 'Label' },
            { name: 'url', label: 'URL', type: 'url' }
          ]
        },
        {
          name: 'themeVariant',
          label: 'Theme Variant',
          type: 'select',
          options: [
            { label: 'Dark', value: 'dark' },
            { label: 'Light', value: 'light' }
          ]
        }
      ],
      preview: function (attributes) {
        return el(Fragment, null, [
          el('h3', { key: 'title' }, attributes.title || 'Hero title'),
          el('p', { key: 'lead' }, attributes.lead || 'Hero lead')
        ]);
      }
    },
    {
      name: 'constructly/how-it-works',
      title: 'Constructly How It Works',
      icon: 'editor-ol',
      previewLabel: 'How It Works',
      attributes: {
        anchor: { type: 'string', default: 'how-it-works' },
        title: { type: 'string', default: '' },
        subtitle: { type: 'string', default: '' },
        ctaLabel: { type: 'string', default: '' },
        ctaUrl: { type: 'string', default: '' },
        steps: {
          type: 'array',
          default: [
            { number: '01', title: '', text: '' },
            { number: '02', title: '', text: '' },
            { number: '03', title: '', text: '' },
            { number: '04', title: '', text: '' }
          ]
        }
      },
      fields: [
        { name: 'anchor', label: 'Anchor ID' },
        { name: 'title', label: 'Title' },
        { name: 'subtitle', label: 'Subtitle', type: 'textarea', rows: 3 },
        { name: 'ctaLabel', label: 'CTA Label' },
        { name: 'ctaUrl', label: 'CTA URL', type: 'url' },
        {
          name: 'steps',
          label: 'Steps',
          type: 'repeater',
          columnsClass: 'bm-editor-grid--2',
          itemLabel: 'Step',
          fields: [
            { name: 'number', label: 'Number' },
            { name: 'title', label: 'Title' },
            { name: 'text', label: 'Text', type: 'textarea', rows: 3 }
          ]
        }
      ],
      preview: function (attributes) {
        return el('h3', null, attributes.title || 'How it works');
      }
    },
    {
      name: 'constructly/popular-calculators',
      title: 'Constructly Popular Calculators',
      icon: 'screenoptions',
      previewLabel: 'Popular Calculators',
      attributes: {
        anchor: { type: 'string', default: 'calculators' },
        title: { type: 'string', default: '' },
        subtitle: { type: 'string', default: '' },
        cards: {
          type: 'array',
          default: [
            { title: '', description: '', buttonLabel: '', buttonUrl: '', icon: 'Ф', previewMediaId: 0, meta: '' },
            { title: '', description: '', buttonLabel: '', buttonUrl: '', icon: 'С', previewMediaId: 0, meta: '' },
            { title: '', description: '', buttonLabel: '', buttonUrl: '', icon: 'К', previewMediaId: 0, meta: '' },
            { title: '', description: '', buttonLabel: '', buttonUrl: '', icon: 'П', previewMediaId: 0, meta: '' },
            { title: '', description: '', buttonLabel: '', buttonUrl: '', icon: 'Г', previewMediaId: 0, meta: '' }
          ]
        }
      },
      fields: [
        { name: 'anchor', label: 'Anchor ID' },
        { name: 'title', label: 'Title' },
        { name: 'subtitle', label: 'Subtitle', type: 'textarea', rows: 3 },
        {
          name: 'cards',
          label: 'Cards',
          type: 'repeater',
          columnsClass: 'bm-editor-grid--2',
          itemLabel: 'Card',
          fields: [
            { name: 'title', label: 'Title' },
            { name: 'description', label: 'Description', type: 'textarea', rows: 3 },
            { name: 'buttonLabel', label: 'Button Label' },
            { name: 'buttonUrl', label: 'Button URL', type: 'url' },
            { name: 'icon', label: 'Icon Letter (fallback)' },
            { name: 'previewMediaId', label: 'Превью (медиатека)', type: 'media' },
            { name: 'meta', label: 'Meta', type: 'textarea', rows: 2 }
          ]
        }
      ],
      preview: function (attributes) {
        return el('h3', null, attributes.title || 'Popular calculators');
      }
    },
    {
      name: 'constructly/why-brigmaster',
      title: 'Constructly Why Brigmaster',
      icon: 'star-filled',
      previewLabel: 'Why Brigmaster',
      attributes: {
        title: { type: 'string', default: '' },
        items: {
          type: 'array',
          default: [
            { title: '', text: '' },
            { title: '', text: '' },
            { title: '', text: '' }
          ]
        }
      },
      fields: [
        { name: 'title', label: 'Title' },
        {
          name: 'items',
          label: 'Items',
          type: 'repeater',
          columnsClass: 'bm-editor-grid--3',
          itemLabel: 'Item',
          fields: [
            { name: 'title', label: 'Title' },
            { name: 'text', label: 'Text', type: 'textarea', rows: 3 }
          ]
        }
      ],
      preview: function (attributes) {
        return el('h3', null, attributes.title || 'Why Brigmaster');
      }
    },
    {
      name: 'constructly/trust',
      title: 'Constructly Trust',
      icon: 'yes-alt',
      previewLabel: 'Trust',
      attributes: {
        title: { type: 'string', default: '' },
        items: {
          type: 'array',
          default: [
            { title: '', text: '' },
            { title: '', text: '' },
            { title: '', text: '' }
          ]
        }
      },
      fields: [
        { name: 'title', label: 'Title' },
        {
          name: 'items',
          label: 'Items',
          type: 'repeater',
          columnsClass: 'bm-editor-grid--3',
          itemLabel: 'Item',
          fields: [
            { name: 'title', label: 'Title' },
            { name: 'text', label: 'Text', type: 'textarea', rows: 3 }
          ]
        }
      ],
      preview: function (attributes) {
        return el('h3', null, attributes.title || 'Trust section');
      }
    },
    {
      name: 'constructly/who-its-for',
      title: 'Constructly Who It Is For',
      icon: 'admin-users',
      previewLabel: 'Audience',
      attributes: {
        title: { type: 'string', default: '' },
        items: { type: 'array', default: ['', '', '', ''] },
        aside: { type: 'string', default: '' }
      },
      fields: [
        { name: 'title', label: 'Title' },
        {
          name: 'items',
          label: 'List Items',
          type: 'string-list',
          itemLabel: 'List Item'
        },
        { name: 'aside', label: 'Aside', type: 'textarea', rows: 3 }
      ],
      preview: function (attributes) {
        return el('h3', null, attributes.title || 'Audience');
      }
    },
    {
      name: 'constructly/how-calculations-work',
      title: 'Constructly How Calculations Work',
      icon: 'editor-help',
      previewLabel: 'How Calculations Work',
      attributes: {
        title: { type: 'string', default: '' },
        text: { type: 'string', default: '' },
        linkLabel: { type: 'string', default: '' },
        linkUrl: { type: 'string', default: '' }
      },
      fields: [
        { name: 'title', label: 'Title' },
        { name: 'text', label: 'Text', type: 'textarea', rows: 4 },
        { name: 'linkLabel', label: 'Link Label' },
        { name: 'linkUrl', label: 'Link URL', type: 'url' }
      ],
      preview: function (attributes) {
        return el('h3', null, attributes.title || 'How calculations work');
      }
    },
    {
      name: 'constructly/final-cta',
      title: 'Constructly Final CTA',
      icon: 'megaphone',
      previewLabel: 'Final CTA',
      attributes: {
        title: { type: 'string', default: '' },
        text: { type: 'string', default: '' },
        buttonLabel: { type: 'string', default: '' },
        buttonUrl: { type: 'string', default: '' }
      },
      fields: [
        { name: 'title', label: 'Title' },
        { name: 'text', label: 'Text', type: 'textarea', rows: 4 },
        { name: 'buttonLabel', label: 'Button Label' },
        { name: 'buttonUrl', label: 'Button URL', type: 'url' }
      ],
      preview: function (attributes) {
        return el('h3', null, attributes.title || 'Final CTA');
      }
    }
  ];

  sectionConfigs.forEach(registerSectionBlock);

  registerSectionBlock({
    name: 'constructly/foundation-hub-hero',
    title: 'Constructly Foundation Hub — Hero',
    icon: 'cover-image',
    previewLabel: 'Foundation Hub Hero',
    attributes: {
      title: { type: 'string', default: '' },
      lead: { type: 'string', default: '' },
      ctaLabel: { type: 'string', default: '' },
      ctaUrl: { type: 'string', default: '#foundation-types' }
    },
    fields: [
      { name: 'title', label: 'Title' },
      { name: 'lead', label: 'Lead', type: 'textarea', rows: 5 },
      { name: 'ctaLabel', label: 'CTA label' },
      { name: 'ctaUrl', label: 'CTA URL', type: 'url' }
    ],
    preview: function (attributes) {
      return el('h3', null, attributes.title || 'Foundation hub hero');
    }
  });

  registerSectionBlock({
    name: 'constructly/foundation-hub-type-cards',
    title: 'Constructly Foundation Hub — Types',
    icon: 'grid-view',
    previewLabel: 'Foundation types',
    attributes: {
      sectionTitle: { type: 'string', default: '' },
      anchorId: { type: 'string', default: 'foundation-types' },
      cards: {
        type: 'array',
        default: [
          { title: '', thesis: '', buttonLabel: '', buttonUrl: '', icon: 'slab' },
          { title: '', thesis: '', buttonLabel: '', buttonUrl: '', icon: 'strip' },
          { title: '', thesis: '', buttonLabel: '', buttonUrl: '', icon: 'pile' }
        ]
      }
    },
    fields: [
      { name: 'sectionTitle', label: 'Section title' },
      { name: 'anchorId', label: 'Section anchor ID' },
      {
        name: 'cards',
        label: 'Cards',
        type: 'repeater',
        columnsClass: 'bm-editor-grid--2',
        itemLabel: 'Card',
        fields: [
          { name: 'title', label: 'Title' },
          { name: 'thesis', label: 'Thesis', type: 'textarea', rows: 3 },
          { name: 'buttonLabel', label: 'Button label' },
          { name: 'buttonUrl', label: 'Button URL', type: 'url' },
          {
            name: 'icon',
            label: 'Icon',
            type: 'select',
            options: [
              { label: 'Slab', value: 'slab' },
              { label: 'Strip', value: 'strip' },
              { label: 'Pile', value: 'pile' }
            ]
          }
        ]
      }
    ],
    preview: function (attributes) {
      return el('h3', null, attributes.sectionTitle || 'Foundation types');
    }
  });

  registerSectionBlock({
    name: 'constructly/foundation-hub-criteria',
    title: 'Constructly Foundation Hub — Criteria',
    icon: 'editor-ol',
    previewLabel: 'Criteria',
    attributes: {
      sectionTitle: { type: 'string', default: '' },
      items: {
        type: 'array',
        default: ['', '', '', '', '']
      }
    },
    fields: [
      { name: 'sectionTitle', label: 'Section title' },
      {
        name: 'items',
        label: 'Criteria (ordered)',
        type: 'string-list',
        itemLabel: 'Criterion'
      }
    ],
    preview: function (attributes) {
      return el('h3', null, attributes.sectionTitle || 'Criteria');
    }
  });

  registerSectionBlock({
    name: 'constructly/foundation-hub-links',
    title: 'Constructly Foundation Hub — Links',
    icon: 'admin-links',
    previewLabel: 'Related links',
    attributes: {
      sectionTitle: { type: 'string', default: '' },
      primaryLinks: {
        type: 'array',
        default: [
          { label: '', url: '' },
          { label: '', url: '' },
          { label: '', url: '' }
        ]
      },
      secondaryLinks: {
        type: 'array',
        default: [
          { label: '', url: '' },
          { label: '', url: '' },
          { label: '', url: '' }
        ]
      }
    },
    fields: [
      { name: 'sectionTitle', label: 'Section title' },
      {
        name: 'primaryLinks',
        label: 'Primary links',
        type: 'repeater',
        itemLabel: 'Link',
        fields: [
          { name: 'label', label: 'Label' },
          { name: 'url', label: 'URL', type: 'url' }
        ]
      },
      {
        name: 'secondaryLinks',
        label: 'Secondary links',
        type: 'repeater',
        itemLabel: 'Link',
        fields: [
          { name: 'label', label: 'Label' },
          { name: 'url', label: 'URL', type: 'url' }
        ]
      }
    ],
    preview: function (attributes) {
      return el('h3', null, attributes.sectionTitle || 'Links');
    }
  });

  registerBlockType('constructly/foundation-hub', {
    apiVersion: 3,
    title: 'Constructly Foundation Hub',
    icon: 'layout',
    category: 'constructly',
    edit: function () {
      var blockProps = useBlockProps({ className: 'bm-editor-block' });
      return el(
        'div',
        blockProps,
        [
          el('span', { className: 'bm-editor-block__label', key: 'label' }, 'Foundation Hub'),
          el(
            'p',
            { key: 'help' },
            'Статичная разметка хаба калькуляторов фундамента (вывод на сервере).'
          )
        ]
      );
    },
    save: function () {
      return null;
    }
  });
})(window.wp);
