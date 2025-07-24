<?php
namespace App\Libraries;

use App\Models\Company;
use App\Libraries\FileLibrary;

class CompanyLibrary
{
    /**
     * Company model instance.
     * 
     * @var \App\Models\Company
     * */
    protected $companyModel;

    /**
     * FileLibrary instance.
     * 
     * @var \App\Libraries\FileLibrary
     */
    protected $fileLibrary;

    /**
     * CompanyLibrary constructor.
     */
    public function __construct()
    {
        $this->companyModel = new Company();
        $this->fileLibrary = new FileLibrary();
    }

    /**
     * Get company by ID.
     *
     * @param int $id
     * @return Company|null
     */
    public function getCompanyById(int $id): ?Company
    {
        return $this->companyModel->find($id);
    }

    /**
     * Get company by slug.
     * 
     * @param string $slug
     * @return Company|null
     */
    public function getCompanyBySlug(string $slug): ?Company
    {
        return $this->companyModel->where('slug', $slug)->first();
    }

    /**
     * Get company cover image.
     * 
     * @param int $companyId
     * @return string|null
     */
    public function getCompanyCoverImage(int $companyId): ?string
    {
        $company = $this->getCompanyById($companyId);

        if (!$company) {
            return null;
        }

        if ($company->cover_id) {
            return $this->fileLibrary->getFile($company->cover_id);
        }

        return null;
    }

    /**
     * Get company logo.
     *
     * @param int $companyId
     * @return string|null
     */
    public function getCompanyLogo(int $companyId): ?string
    {
        $company = $this->getCompanyById($companyId);

        if (!$company) {
            return null;
        }

        if ($company->logo_id) {
            return $this->fileLibrary->getFile($company->logo_id);
        }

        return null;
    }

    /**
     * Create a new company.
     *
     * @param array $data
     * @return Company
     */
    public function createCompany(array $data): Company
    {
        // Lets check if the company slug already exists
        if ($this->companyModel->where('slug', $data['slug'])->exists()) {
            throw new \Exception('Company with this slug already exists.');
        }

        // Handle cover and logo uploads
        if (!empty($data['cover_picture'])) {
            $cover = $this->fileLibrary->upload($data['cover_picture']);

            if (!$cover) {
                throw new \Exception('Failed to upload cover picture.');
            }
            $data['cover_id'] = $cover->id;
        }

        if (!empty($data['logo_picture'])) {
            $logo = $this->fileLibrary->upload($data['logo_picture']);

            if (!$logo) {
                throw new \Exception('Failed to upload logo picture.');
            }
            $data['logo_id'] = $logo->id;
        }

        // Create the company record
        return $this->companyModel->create($data);
    }
}