/**
 * @package: 	WeCodeArt CF7 Extension
 * @author: 	Bican Marian Valeriu
 * @license:	https://www.wecodeart.com/
 * @version:	1.0.0
 */

const {
    i18n: {
        __,
        sprintf
    },
    hooks: {
        addFilter
    },
    components: {
        Placeholder,
        ToggleControl,
        SelectControl,
        Dashicon,
        Spinner,
        Tooltip,
        Button,
    },
    element: {
        useState,
        useEffect
    }
} = wp;

addFilter('wecodeart.admin.extensions', 'wecodeart/cf7/admin/panel', optionsPanel);
function optionsPanel(panels) {
    return [...panels, {
        name: 'wca-cf7',
        title: __('Contact Form 7', 'wca-cf7'),
        render: (props) => <Options {...props} />
    }];
}

const Options = (props) => {
    const { isRequesting, wecodeartSettings, saveEntityRecord, createNotice } = props;

    if (isRequesting || !wecodeartSettings) {
        return <Placeholder {...{
            icon: <Spinner />,
            label: __('Loading', 'wca-cf7'),
            instructions: __('Please wait, loading settings...', 'wca-cf7')
        }} />;
    }

    const [loading, setLoading] = useState(null);
    const apiOptions = (({ cf7_clean_assets, cf7_remove_js, cf7_remove_css, cf7_remove_autop }) => ({ cf7_clean_assets, cf7_remove_js, cf7_remove_css, cf7_remove_autop }))(wecodeartSettings);
    const [formData, setFormData] = useState(apiOptions);

    const handleNotice = () => {
        setLoading(false);
        return createNotice('success', __('Settings saved.', 'wca-cf7'));
    };

    const getHelpText = (type) => {
        let text = '', status = '';

        switch (type) {
            case 'assets':
                status = formData['cf7_clean_assets'] ? __('when the content has a form', 'wca-cf7') : __('on every page', 'wca-cf7');
                text = sprintf(__('Contact Form 7 assets are loaded %s.', 'wca-cf7'), status);
                break;
            case 'JS':
                status = formData['cf7_remove_js'] ? __('removed', 'wca-cf7') : __('loaded', 'wca-cf7');
                text = sprintf(__('Default Contact Form 7 plugin JS will be %s.', 'wca-cf7'), status);
                break;
            case 'CSS':
                status = formData['cf7_remove_css'] ? __('removed', 'wca-cf7') : __('loaded', 'wca-cf7');
                text = sprintf(__('Default Contact Form 7 plugin CSS will be %s.', 'wca-cf7'), status);
                break;
            case 'P':
                status = formData['cf7_remove_autop'] ? __('does not', 'wca-cf7') : __('does', 'wca-cf7');
                text = sprintf(__('Contact Form 7 %s apply the "autop" filter to the form content.', 'wca-cf7'), status);
                break;
            default:
        }

        return text;
    };

    const assetsControl = (formData['cf7_remove_js'] === true && formData['cf7_remove_css'] === true) === false;

    useEffect(() => {
        if (!assetsControl) {
            setFormData({ ...formData, 'cf7_clean_assets': '' });
        }
    }, [assetsControl]);

    return (
        <>
            <ToggleControl
                label={<>
                    {__('Remove JS?', 'wca-cf7')}
                    <Tooltip text={__('Removing JS will cause the form submission to hard refresh the page!', 'wca-cf7')}>
                        <Dashicon icon="editor-help" style={{ marginLeft: '1rem', color: 'var(--wp-admin-theme-color)' }} />
                    </Tooltip>
                </>}
                help={getHelpText('JS')}
                checked={formData['cf7_remove_js']}
                onChange={value => setFormData({ ...formData, 'cf7_remove_js': value ? value : '' })}
            />
            <ToggleControl
                label={__('Remove CSS?', 'wca-cf7')}
                help={getHelpText('CSS')}
                checked={formData['cf7_remove_css']}
                onChange={value => setFormData({ ...formData, 'cf7_remove_css': value ? value : '' })}
            />
            {assetsControl && (
                <ToggleControl
                    label={__('Optimize assets loading?', 'wca-cf7')}
                    help={getHelpText('assets')}
                    checked={formData['cf7_clean_assets']}
                    onChange={value => setFormData({ ...formData, 'cf7_clean_assets': value ? value : '' })}
                />
            )}
            <ToggleControl
                label={__('Remove "autop" filter?', 'wca-cf7')}
                help={getHelpText('P')}
                checked={formData['cf7_remove_autop']}
                onChange={value => setFormData({ ...formData, 'cf7_remove_autop': value ? value : '' })}
            />
            <hr style={{ margin: '20px 0' }} />
            <Button
                className="button"
                isPrimary
                isLarge
                icon={loading && <Spinner />}
                onClick={() => {
                    setLoading(true);
                    let value = {};
                    Object.keys(formData).map(k => value = { ...value, [k]: formData[k] === '' ? 'unset' : formData[k] });
                    saveEntityRecord('wecodeart', 'settings', value).then(handleNotice);
                }}
                {...{ disabled: loading }}
            >
                {loading ? '' : __('Save', 'wecodeart')}
            </Button>
        </>
    );
};