{extends file='finance/template.tpl'}
{block name=title}{$Helper->getConfig()->getCompanyName()} {$View->page_title}{/block}
{block name=body}
    <div class="page-header">
        <h3>Welcome</h3>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="jumbotron">
                <p>
                    Loan releases and collection
                </p>
                <p>
                    <a class="btn btn-primary btn-lg" href="lending/">
                        <i class="fa fa-users" aria-hidden="true"></i> Financing
                    </a>
                </p>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="jumbotron">
                <p>
                    Bank reconciliation
                </p>
                <p>
                    <a class="btn btn-primary btn-lg" href="cash/">
                        <i class="fa fa-usd" aria-hidden="true"></i> Cash Position
                    </a>
                </p>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="jumbotron">
                <p>
                    Employee accounts and payslips
                </p>
                <p>
                    <a class="btn btn-primary btn-lg" href="payroll/">
                        <i class="fa fa-bar-chart" aria-hidden="true"></i> Payroll
                    </a>
                </p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="jumbotron">
                <p>
                    Pensioner Information
                </p>
                <p>
                    <a class="btn btn-primary btn-lg" href="pensioner/">
                        <i class="fa fa-table" aria-hidden="true"></i> Pensioner
                    </a>
                </p>
            </div>
        </div>

    </div>
{/block}