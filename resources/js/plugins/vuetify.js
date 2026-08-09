import 'vuetify/styles';
import { createVuetify } from 'vuetify';

const brand = {
    primary: '#ab3324',
    secondary: '#1f4d78',
    success: '#3e7f5b',
    error: '#c1503f',
    warning: '#d98a34',
    info: '#2e5e8c',
};

export default createVuetify({
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                dark: false,
                colors: {
                    ...brand,
                    background: '#fffdf8',
                    surface: '#ffffff',
                },
            },
            dark: {
                dark: true,
                colors: {
                    ...brand,
                    background: '#1b2420',
                    surface: '#232f29',
                },
            },
        },
    },
});
