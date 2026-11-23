import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { Refrigerator } from 'lucide-react';

type Appliance = {
    id: number;
    appliance_type: string;
    model_number: string;
    serial_number: string;
    purchase_date: string;
    customer: { name: string; phone: string };
    warranty_policy: { warranty_expiry: string };
};

type Paginated<T> = {
    data: T[];
    last_page: number;
    links: { url: string | null; label: string; active: boolean }[];
};

export default function ApplianceIndex({ appliances }: { appliances: Paginated<Appliance> }) {
    return (
        <AppLayout breadcrumbs={[{ title: 'Appliances', href: '/appliances' }]}>
            <Head title="Appliances" />
            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Registered appliances</h1>
                    <Button asChild>
                        <Link href={route('appliances.create')}>Register appliance</Link>
                    </Button>
                </div>
                {appliances.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto rounded-lg border">
                            <table className="w-full text-left text-sm">
                                <thead className="bg-muted">
                                    <tr>
                                        <th className="p-3">Appliance</th>
                                        <th className="p-3">Model / serial</th>
                                        <th className="p-3">Customer</th>
                                        <th className="p-3">Purchased</th>
                                        <th className="p-3">Warranty expiry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {appliances.data.map((appliance) => (
                                        <tr key={appliance.id} className="border-t">
                                            <td className="p-3">{appliance.appliance_type}</td>
                                            <td className="p-3">
                                                {appliance.model_number} / {appliance.serial_number}
                                            </td>
                                            <td className="p-3">
                                                {appliance.customer.name} ({appliance.customer.phone})
                                            </td>
                                            <td className="p-3">{appliance.purchase_date}</td>
                                            <td className="p-3">{appliance.warranty_policy.warranty_expiry}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        {appliances.last_page > 1 && (
                            <nav className="flex flex-wrap gap-2" aria-label="Appliance pagination">
                                {appliances.links.map((link, index) => (
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
                        <Refrigerator className="mx-auto size-9 text-muted-foreground" aria-hidden="true" />
                        <h2 className="mt-4 font-medium">No appliances registered yet</h2>
                        <p className="mt-1 text-sm text-muted-foreground">Register an appliance to keep its warranty and customer details in one place.</p>
                        <Button className="mt-5" asChild>
                            <Link href={route('appliances.create')}>Register your first appliance</Link>
                        </Button>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
