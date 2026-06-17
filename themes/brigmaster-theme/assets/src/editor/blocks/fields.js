const { createElement: h } = window.wp.element;
const { Button, PanelBody, TextControl, TextareaControl } = window.wp.components;
const { MediaUpload, MediaUploadCheck } = window.wp.blockEditor;

function updateArrayItem(items, index, nextItem) {
  return items.map((item, itemIndex) => (itemIndex === index ? nextItem : item));
}

function moveArrayItem(items, index, direction) {
  const nextIndex = index + direction;
  if (nextIndex < 0 || nextIndex >= items.length) {
    return items;
  }

  const nextItems = [...items];
  const current = nextItems[index];
  nextItems[index] = nextItems[nextIndex];
  nextItems[nextIndex] = current;
  return nextItems;
}

function renderTextField(field, value, onChange) {
  const Component = field.type === 'textarea' ? TextareaControl : TextControl;
  return h(Component, {
    key: field.name,
    label: field.label,
    value: value || '',
    rows: field.type === 'textarea' ? 4 : undefined,
    onChange,
  });
}

function renderImageField(field, value, onChange) {
  return h(
    'div',
    { key: field.name, className: 'bm-editor-image-field' },
    h(TextControl, {
      label: field.label,
      value: value || '',
      onChange,
    }),
    h(
      MediaUploadCheck,
      null,
      h(MediaUpload, {
        allowedTypes: ['image'],
        value: value || '',
        onSelect: (media) => onChange(media?.url || ''),
        render: ({ open }) =>
          h(
            Button,
            {
              variant: 'secondary',
              onClick: open,
            },
            value ? 'Заменить изображение' : 'Выбрать изображение',
          ),
      }),
    ),
    value ? h(Button, { isDestructive: true, onClick: () => onChange('') }, 'Удалить изображение') : null,
  );
}

function renderScalarField(field, value, onChange) {
  if (field.type === 'image') {
    return renderImageField(field, value, onChange);
  }

  return renderTextField(field, value, onChange);
}

function renderRepeater(field, attributes, setAttributes) {
  const items = Array.isArray(attributes[field.name]) ? attributes[field.name] : [];

  return h(
    PanelBody,
    {
      key: field.name,
      title: field.label,
      initialOpen: false,
    },
    items.map((item, index) =>
      h(
        PanelBody,
        {
          key: `${field.name}-${index}`,
          title: `${field.label}: ${index + 1}`,
          initialOpen: false,
        },
        field.itemFields.map((itemField) =>
          renderScalarField(itemField, item?.[itemField.name], (value) => {
            setAttributes({
              [field.name]: updateArrayItem(items, index, {
                ...item,
                [itemField.name]: value,
              }),
            });
          }),
        ),
        h(
          'div',
          { className: 'bm-editor-repeater-actions' },
          h(Button, {
            variant: 'secondary',
            disabled: index === 0,
            onClick: () => setAttributes({ [field.name]: moveArrayItem(items, index, -1) }),
          }, 'Выше'),
          h(Button, {
            variant: 'secondary',
            disabled: index === items.length - 1,
            onClick: () => setAttributes({ [field.name]: moveArrayItem(items, index, 1) }),
          }, 'Ниже'),
          h(Button, {
            isDestructive: true,
            onClick: () => setAttributes({ [field.name]: items.filter((_, itemIndex) => itemIndex !== index) }),
          }, 'Удалить'),
        ),
      ),
    ),
    h(Button, {
      variant: 'primary',
      onClick: () => setAttributes({ [field.name]: [...items, {}] }),
    }, 'Добавить'),
  );
}

function renderObjectField(field, attributes, setAttributes) {
  const value = attributes[field.name] && typeof attributes[field.name] === 'object' ? attributes[field.name] : {};

  return h(
    PanelBody,
    {
      key: field.name,
      title: field.label,
      initialOpen: false,
    },
    field.fields.map((childField) =>
      renderScalarField(childField, value[childField.name], (nextValue) => {
        setAttributes({
          [field.name]: {
            ...value,
            [childField.name]: nextValue,
          },
        });
      }),
    ),
  );
}

export function renderFields(fields, attributes, setAttributes) {
  return fields.map((field) => {
    if (field.type === 'repeater') {
      return renderRepeater(field, attributes, setAttributes);
    }

    if (field.type === 'object') {
      return renderObjectField(field, attributes, setAttributes);
    }

    return renderScalarField(field, attributes[field.name], (value) => {
      setAttributes({ [field.name]: value });
    });
  });
}
