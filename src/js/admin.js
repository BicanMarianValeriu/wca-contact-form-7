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
        DropdownMenu,
        ToggleControl,
        SelectControl,
        Card,
        CardHeader,
        CardBody,
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

const POSITIONS = {
    modal: [
        { label: __('Top', 'wca-cf7'), value: 'top' },
        { label: __('Middle', 'wca-cf7'), value: 'centered' },
    ],
    toast: [
        { label: __('Top Start', 'wca-cf7'), value: 'top-0 start-0' },
        { label: __('Top Center', 'wca-cf7'), value: 'top-0 start-50 translate-middle-x' },
        { label: __('Top End', 'wca-cf7'), value: 'top-0 end-0' },
        { label: __('Middle Start', 'wca-cf7'), value: 'top-50 start-0 translate-middle-y' },
        { label: __('Middle Center', 'wca-cf7'), value: 'top-50 start-50 translate-middle' },
        { label: __('Middle End', 'wca-cf7'), value: 'top-50 end-0 translate-middle-y' },
        { label: __('Bottom Start', 'wca-cf7'), value: 'bottom-0 start-0' },
        { label: __('Bottom Center', 'wca-cf7'), value: 'bottom-0 start-50 translate-middle-x' },
        { label: __('Bottom End', 'wca-cf7'), value: 'bottom-0 end-0' },
    ],
};

addFilter('wecodeart.admin.tabs.plugins', 'wecodeart/cf7/admin/panel', optionsPanel);
function optionsPanel(panels) {
    return [...panels, {
        name: 'wca-cf7',
        title: __('Contact Form 7', 'wca-cf7'),
        render: (props) => <Options {...props} />
    }];
}

const Options = (props) => {
    const { settings, saveSettings, isRequesting, createNotice } = props;

    if (isRequesting || !settings) {
        return <Placeholder {...{
            icon: <Spinner />,
            label: __('Loading', 'wca-cf7'),
            instructions: __('Please wait, loading settings...', 'wca-cf7')
        }} />;
    }

    const [loading, setLoading] = useState(null);
    const apiOptions = (({ contact_form_7 }) => (contact_form_7))(settings);
    const [formData, setFormData] = useState(apiOptions);

    const handleNotice = () => {
        setLoading(false);
        return createNotice('success', __('Settings saved.', 'wca-cf7'));
    };

    const getHelpText = (type) => {
        let text = '', status = '';

        switch (type) {
            case 'assets':
                status = formData?.clean_assets ? __('when the content has a form', 'wca-cf7') : __('on every page', 'wca-cf7');
                text = sprintf(__('Contact Form 7 assets are loaded %s.', 'wca-cf7'), status);
                break;
            case 'JS':
                status = formData?.remove_js ? __('removed', 'wca-cf7') : __('loaded', 'wca-cf7');
                text = sprintf(__('Default Contact Form 7 plugin JS will be %s.', 'wca-cf7'), status);
                break;
            case 'CSS':
                status = formData?.remove_css ? __('removed', 'wca-cf7') : __('loaded', 'wca-cf7');
                text = sprintf(__('Default Contact Form 7 plugin CSS will be %s.', 'wca-cf7'), status);
                break;
            case 'P':
                status = formData?.remove_autop ? __('does not', 'wca-cf7') : __('does', 'wca-cf7');
                text = sprintf(__('Contact Form 7 %s apply the "autop" filter to the form content.', 'wca-cf7'), status);
                break;
            case 'feedback':
                text = __('Select submission feedback type.', 'wca-cf7');
                break;
            default:
        }

        return text;
    };

    const assetsControl = !(formData?.remove_js && formData.remove_css);

    useEffect(() => {
        if (!assetsControl) {
            setFormData({ ...formData, clean_assets: false });
        }
    }, [assetsControl]);

    return (
        <>
            <div className="grid" style={{ '--wca--columns': 2 }}>
                <div className="g-col-2 g-col-lg-1">
                    <Card className="border shadow-none h-100">
                        <CardHeader>
                            <h5 className="text-uppercase fw-medium m-0">{__('Optimization', 'wca-cf7')}</h5>
                        </CardHeader>
                        <CardBody>
                            <ToggleControl
                                label={<>
                                    <span style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                        <span>{__('Remove JS?', 'wca-cf7')}</span>
                                        <DropdownMenu
                                            label={__('More Information', 'wca-cf7')}
                                            icon={<Dashicon icon="info" style={{ color: 'var(--wca--header--color)' }} />}
                                            toggleProps={{
                                                style: {
                                                    height: 'initial',
                                                    minWidth: 'initial',
                                                    padding: 0
                                                }
                                            }}
                                            popoverProps={{
                                                focusOnMount: 'container',
                                                position: 'bottom',
                                                noArrow: false,
                                            }}
                                        >
                                            {() => (
                                                <p style={{ minWidth: 250, margin: 0 }}>
                                                    {__('Removing JS will cause the form submission to hard refresh the page!', 'wca-cf7')}
                                                </p>
                                            )}
                                        </DropdownMenu>
                                    </span>
                                </>}
                                help={getHelpText('JS')}
                                checked={formData?.remove_js}
                                onChange={remove_js => setFormData({ ...formData, remove_js, feedback: '' })}
                            />
                            <ToggleControl
                                label={__('Remove CSS?', 'wca-cf7')}
                                help={getHelpText('CSS')}
                                checked={formData?.remove_css}
                                onChange={remove_css => setFormData({ ...formData, remove_css })}
                            />
                            {assetsControl && (
                                <ToggleControl
                                    label={__('Optimize assets loading?', 'wca-cf7')}
                                    help={getHelpText('assets')}
                                    checked={formData?.clean_assets}
                                    onChange={clean_assets => setFormData({ ...formData, clean_assets })}
                                />
                            )}
                        </CardBody>
                    </Card>
                </div>
                <div className="g-col-2 g-col-lg-1">
                    <Card className="border shadow-none h-100">
                        <CardHeader>
                            <h5 className="text-uppercase fw-medium m-0">{__('Functionality', 'wca-cf7')}</h5>
                        </CardHeader>
                        <CardBody>
                            <ToggleControl
                                label={<>
                                    <span style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                        <span>{__('Remove "autop" filter?', 'wca-cf7')}</span>
                                        <DropdownMenu
                                            label={__('More Information', 'wca-cf7')}
                                            icon={<Dashicon icon="info" style={{ color: 'var(--wca--header--color)' }} />}
                                            toggleProps={{
                                                style: {
                                                    height: 'initial',
                                                    minWidth: 'initial',
                                                    padding: 0
                                                }
                                            }}
                                            popoverProps={{
                                                focusOnMount: 'container',
                                                position: 'bottom',
                                                noArrow: false,
                                            }}
                                        >
                                            {() => (
                                                <p style={{ minWidth: 250, margin: 0 }}>
                                                    {__('Removing this filter will alow the use of HTML tags in your forms.', 'wca-cf7')}
                                                </p>
                                            )}
                                        </DropdownMenu>
                                    </span>
                                </>}
                                help={getHelpText('P')}
                                checked={formData?.remove_autop}
                                onChange={remove_autop => setFormData({ ...formData, remove_autop })}
                            />
                            {!formData?.remove_js && <>
                                <SelectControl
                                    label={__('Feedback type', 'wca-cf7')}
                                    value={formData?.feedback}
                                    options={[
                                        { label: __('Default', 'wca-cf7'), value: '' },
                                        { label: __('Modal', 'wca-cf7'), value: 'modal' },
                                        { label: __('Toast', 'wca-cf7'), value: 'toast' },
                                    ]}
                                    onChange={(feedback) => setFormData({ ...formData, feedback, feedback_position: '' })}
                                    help={getHelpText('feedback')}
                                />
                                {formData?.feedback !== '' && <SelectControl
                                    label={__('Feedback position', 'wca-cf7')}
                                    value={formData?.feedback_position}
                                    options={POSITIONS[formData?.feedback]}
                                    onChange={(feedback_position) => setFormData({ ...formData, feedback_position })}
                                />}
                            </>}
                        </CardBody>
                    </Card>
                </div>
            </div>
            <hr style={{ margin: '20px 0' }} />
            <Button
                className="button"
                isPrimary
                isLarge
                icon={loading && <Spinner />}
                onClick={() => {
                    setLoading(true);
                    saveSettings({ contact_form_7: formData }, handleNotice);
                }}
                {...{ disabled: loading }}
            >
                {loading ? '' : __('Save', 'wecodeart')}
            </Button>
        </>
    );
};