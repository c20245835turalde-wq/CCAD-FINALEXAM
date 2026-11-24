import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { ArrowRight, Clock3, FilePlus2, PackagePlus, Refrigerator, ShieldCheck } from 'lucide-react';

type DashboardStats = {
    appliances: number;
    activeWarranties: number;
    pendingClaims: number;
    approvedClaims: number;
};

type RecentClaim = {
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
};

const summaryItems = [
    { key: 'appliances', label: 'Registered appliances', icon: Refrigerator, href: '/appliances' },
    { key: 'activeWarranties', label: 'Active warranties', icon: ShieldCheck, href: '/appliances' },
    { key: 'pendingClaims', label: 'Pending claims', icon: Clock3, href: '/warranty-claims' },
    { key: 'approvedClaims', label: 'Approved claims', icon: ShieldCheck, href: '/warranty-claims' },
] as const;

export default function Dashboard({ stats, recentClaims }: { stats: DashboardStats; recentClaims: RecentClaim[] }) {
    return (
        <AppLayout breadcrumbs={[{ title: 'Dashboard', href: '/dashboard' }]}>
            <Head title="Dashboard" />
            <main className="space-y-8 p-4 md:p-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">Dashboard</h1>
                        <p className="text-muted-foreground mt-1 text-sm">An overview of your appliances and warranty claims.</p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Button variant="outline" asChild>
                            <Link href="/appliances/create">
                                <PackagePlus data-icon="inline-start" />
                                Register appliance
                            </Link>
                        </Button>
                        <Button asChild>
                            <Link href="/warranty-claims/create">
                                <FilePlus2 data-icon="inline-start" />
                                File a claim
                            </Link>
                        </Button>
                    </div>
                </div>

                <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Summary">
                    {summaryItems.map(({ key, label, icon: Icon, href }) => (
                        <Card key={key}>
                            <CardHeader className="flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-muted-foreground text-sm font-medium">{label}</CardTitle>
                                <Icon className="text-muted-foreground size-4" aria-hidden="true" />
                            </CardHeader>
                            <CardContent>
                                <p className="text-3xl font-semibold">{stats[key]}</p>
                                <Link href={href} className="text-muted-foreground hover:text-foreground mt-3 inline-flex items-center gap-1 text-sm">
                                    View details <ArrowRight className="size-3.5" aria-hidden="true" />
                                </Link>
                            </CardContent>
                        </Card>
                    ))}
                </section>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-lg">Recent warranty claims</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {recentClaims.length > 0 ? (
                            <div className="divide-y">
                                {recentClaims.map((claim) => (
                                    <div
                                        key={claim.id}
                                        className="flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between"
                                    >
                                        <div className="min-w-0">
                                            <p className="font-medium">
                                                {claim.warranty_policy.appliance.appliance_type}
                                                <span className="text-muted-foreground font-normal">
                                                    {' '}
                                                    · {claim.warranty_policy.appliance.customer.name}
                                                </span>
                                            </p>
                                            <p className="text-muted-foreground mt-1 truncate text-sm">
                                                {claim.defect_description} · Serial {claim.warranty_policy.appliance.serial_number}
                                            </p>
                                        </div>
                                        <div className="flex shrink-0 items-center gap-3 text-sm">
                                            <span className="text-muted-foreground">{claim.claim_date}</span>
                                            <span className="bg-muted rounded-full px-2.5 py-1 font-medium capitalize">{claim.claim_status}</span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="rounded-lg border border-dashed px-6 py-10 text-center">
                                <ShieldCheck className="text-muted-foreground mx-auto size-8" aria-hidden="true" />
                                <h2 className="mt-3 font-medium">No warranty claims yet</h2>
                                <p className="text-muted-foreground mt-1 text-sm">Claims you file will show up here.</p>
                                <Button className="mt-4" variant="outline" asChild>
                                    <Link href="/warranty-claims/create">File your first claim</Link>
                                </Button>
                            </div>
                        )}
                        {recentClaims.length > 0 && (
                            <div className="mt-5 border-t pt-4">
                                <Link href="/warranty-claims" className="inline-flex items-center gap-1 text-sm font-medium hover:underline">
                                    View all claims <ArrowRight className="size-3.5" aria-hidden="true" />
                                </Link>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}
