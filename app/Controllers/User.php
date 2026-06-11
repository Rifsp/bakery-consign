<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $this->checkAccess();

        $search = trim($this->request->getGet('search') ?? '');
        $role = $this->request->getGet('role');

        $query = $this->userModel;

        if ($search !== '') {
            $query->groupStart()
                ->like('nama', $search)
                ->orLike('username', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }
        if ($role && $role !== '') {
            $query->where('role', $role);
        }

        $data = [
            'title' => 'User',
            'records' => $query->orderBy('id', 'DESC')->findAll(),
            'search' => $search,
            'filterRole' => $role,
        ];
        return view('user/index', $data);
    }

    public function create()
    {
        $this->checkAccess();
        $data = ['title' => 'Tambah User', 'record' => null];
        return view('user/form', $data);
    }

    public function store()
    {
        $this->checkAccess();

        $rules = [
            'username' => 'required|is_unique[users.username]|min_length[3]',
            'nama' => 'required',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[admin,sales]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'username' => $this->request->getPost('username'),
            'nama' => $this->request->getPost('nama'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'is_aktif' => (bool) $this->request->getPost('is_aktif'),
        ]);

        return redirect()->to('/user')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->checkAccess();
        $record = $this->userModel->find($id);
        if (!$record) {
            return redirect()->to('/user')->with('error', 'User tidak ditemukan');
        }
        $data = ['title' => 'Edit User', 'record' => $record];
        return view('user/form', $data);
    }

    public function update($id)
    {
        $this->checkAccess();
        $record = $this->userModel->find($id);
        if (!$record) {
            return redirect()->to('/user')->with('error', 'User tidak ditemukan');
        }

        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username,id,' . $id . ']',
            'nama' => 'required',
            'role' => 'required|in_list[admin,sales]',
        ];
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'nama' => $this->request->getPost('nama'),
            'role' => $this->request->getPost('role'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'is_aktif' => (bool) $this->request->getPost('is_aktif'),
        ];
        if ($pw = $this->request->getPost('password')) {
            $data['password'] = password_hash($pw, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);
        return redirect()->to('/user')->with('success', 'User berhasil diupdate');
    }

    public function delete($id)
    {
        $this->checkAccess();
        $record = $this->userModel->find($id);
        if (!$record) {
            return redirect()->to('/user')->with('error', 'User tidak ditemukan');
        }

        // Toggle active instead of hard delete
        $this->userModel->update($id, ['is_aktif' => !$record['is_aktif']]);
        $status = $record['is_aktif'] ? 'dinonaktifkan' : 'diaktifkan';
        return redirect()->to('/user')->with('success', "User berhasil {$status}");
    }

    protected function checkAccess(): void
    {
        if (session()->get('role') !== 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
