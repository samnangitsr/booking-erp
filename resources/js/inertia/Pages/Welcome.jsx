import { Head, Link } from '@inertiajs/react';

export default function Welcome({ appName = 'Booking ERP', locale = 'en' }) {
    return (
        <>
            <Head title="Welcome" />
            <div className="container py-5">
                <div className="text-center">
                    <h1 className="display-5 fw-bold">{appName}</h1>
                    <p className="lead text-muted mb-4">
                        {locale === 'km'
                            ? 'ប្រព័ន្ធគ្រប់គ្រងការកក់ដែលរួមបញ្ចូលនូវសាខាច្រើន'
                            : 'Multi-branch hospitality booking ERP'}
                    </p>
                    <Link
                        href="/admin/login"
                        className="btn btn-primary btn-lg"
                    >
                        {locale === 'km' ? 'ចូលផ្ទាំងគ្រប់គ្រង' : 'Enter Admin'}
                    </Link>
                </div>
            </div>
        </>
    );
}
