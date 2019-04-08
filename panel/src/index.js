import SingleImport from './components/SingleImport/SingleImport.vue';
import BatchImport from './components/BatchImport/BatchImport.vue';

panel.plugin('tillprochaska/social-import', {
    sections: {
        socialImportSingle: SingleImport,
    },
    views: {
        socialImportBatch: {
            label: 'Batch Import',
            icon: 'import',
            component: BatchImport,
        },
    },
});