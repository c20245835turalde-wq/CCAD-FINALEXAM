import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { ShieldCheck } from 'lucide-react';

type Claim = {
    id: number;
    claim_date: string;
    defect_description: string;
    claim_status: string;
    warranty_policy: {
        appliance: {
            appliance_type: string;
            serial_number: string;
            customer: { name: string };
        };
    };
    payout: { approved_amount: string } | null;
};

type Paginated<T> = {
    data: T[];
    last_page: number;
    links: { url: string | null; label: string; active: boolean }[];
};

export default function WarrantyClaimIndex({ claims, hasAppliances }: { claims: Paginated<Claim>; hasAppliances: boolean }) {
    return (
        <AppLayout breadcrumbs={[{ title: 'Warranty claims', href: '/warranty-claims' }]}>
            <Head title="Warranty claims" />
            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Warranty claims</h1>
                    <Button asChild>
                        <Link href={hasAppliances ? route('warranty-claims.create') : route('appliances.create')}>
                            {hasAppliances ? 'File claim' : 'Register appliance'}
                        </Link>
                    </Button>
                </div>
                {claims.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto rounded-lg border">
                            <table className="w-full text-left text-sm">
                                <thead className="bg-muted">
                                    <tr>
                                        <th className="p-3">Date</th>
                                        <th className="p-3">Customer / appliance</th>
                                        <th className="p-3">Defect</th>
                                        <th className="p-3">Status</th>
                                        <th className="p-3">Approved amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {claims.data.map((claim) => (
                                        <tr key={claim.id} className="border-t">
                                            <td className="p-3">{claim.claim_date}</td>
                                            <td className="p-3">
                                                {claim.warranty_policy.appliance.customer.name} / {claim.warranty_policy.appliance.appliance_type} ({claim.warranty_policy.appliance.serial_number})
                                            </td>
                                            <td className="max-w-sm p-3">{claim.defect_description}</td>
                                            <td className="p-3 capitalize">{claim.claim_status}</td>
                                            <td className="p-3">{claim.payout?.approved_amount ?? '—'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        {claims.last_page > 1 && (
                            <nav className="flex flex-wrap gap-2" aria-label="Warranty claim pagination">
                                {claims.links.map((link, index) => (
                                    <Link
                                        key={`${link.label}-${index}`}
                                        href={link.url ?? '#'}
                                        preserveScroll
                                        className={`rounded border px-3 py-1 text-sm ${link.active ? 'bg-primary text-primary-foreground' : ''} ${link.url ? '' : 'pointer-events-none opacity-50'}`}
                                    >
                                        {link.label.replace(/&laquo;|&raquo;/g, '')}
                                    </Link>
                                ))}
                            </nav>
                        )}
                    </>
                ) : (
                    <div className="rounded-lg border border-dashed px-6 py-12 text-center">
                        <ShieldCheck className="mx-auto size-9 text-muted-foreground" aria-hidden="true" />
                        <h2 className="mt-4 font-medium">No warranty claims filed yet</h2>
                        <p className="mt-1 text-sm text-muted-foreground">
                            {hasAppliances ? 'Claims filed for your registered appliances will appear here.' : 'Register an appliance first so you can file a claim against its warranty.'}
                        </p>
                        <Button className="mt-5" variant={hasAppliances ? 'default' : 'outline'} asChild>
                            <Link href={hasAppliances ? route('warranty-claims.create') : route('appliances.create')}>
                                {hasAppliances ? 'File your first claim' : 'Register an appliance'}
                            </Link>
                        </Button>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
